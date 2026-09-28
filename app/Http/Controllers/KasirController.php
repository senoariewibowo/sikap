<?php

namespace App\Http\Controllers;

use App\Models\KasirPayment;
use App\Models\KasirTransaksi;
use App\Models\KasirTransaksiDetail;
use App\Models\Product;
use App\Models\ProductGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function pos()
    {
        $groups = ProductGroup::with(['products' => fn($q) => $q->where('is_active', true)->orderBy('product_name')])
            ->orderBy('group_name')->get();

        return view('kasir.pos', compact('groups'));
    }

    public function productsJson()
    {
        $groups = ProductGroup::with(['products' => function ($q) {
            $q->where('is_active', true)->orderBy('product_name');
        }])->orderBy('group_name')->get();

        $products = Product::with(['prices', 'group'])->where('is_active', true)->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'barcode' => $p->barcode,
                'product_name' => $p->product_name,
                'group_id' => $p->group_id,
                'group_name' => $p->group?->group_name,
                'prices' => $p->prices->map(fn($pr) => [
                    'tier' => $pr->tier,
                    'min_qty' => $pr->min_qty,
                    'harga' => (float) $pr->harga,
                ])->values(),
            ])->values();

        return response()->json([
            'groups' => $groups->map(fn($g) => ['id' => $g->id, 'group_name' => $g->group_name])->values(),
            'products' => $products,
        ]);
    }

    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        $products = Product::with(['prices', 'group'])
            ->where('is_active', true)
            ->when($q !== '', fn($query) => $query->where(function ($sq) use ($q) {
                $sq->where('barcode', 'like', "%{$q}%")
                    ->orWhere('product_name', 'like', "%{$q}%");
            }))
            ->orderBy('product_name')
            ->limit(20)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'barcode' => $p->barcode,
                'product_name' => $p->product_name,
                'group_id' => $p->group_id,
                'group_name' => $p->group?->group_name,
                'prices' => $p->prices->map(fn($pr) => [
                    'tier' => $pr->tier,
                    'min_qty' => $pr->min_qty,
                    'harga' => (float) $pr->harga,
                ])->values(),
            ])->values();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer',
            'dp' => 'required|numeric|min:0',
            'nama_pembeli' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'nullable|numeric|min:0',
        ]);

        $total = 0;
        $rows = [];

        foreach ($request->items as $item) {
            $product = Product::with('prices')->findOrFail($item['product_id']);
            $qty = (int) $item['qty'];
            if (isset($item['harga']) && is_numeric($item['harga'])) {
                $harga = (float) $item['harga'];
            } else {
                $harga = $product->priceForQty($qty) ?? ($product->prices->first()->harga ?? 0);
            }
            $subtotal = $harga * $qty;
            $total += $subtotal;
            $rows[] = [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'harga' => $harga,
                'qty' => $qty,
                'subtotal' => $subtotal,
                'modal' => $product->modal ?? 0,
            ];
        }

        $total = round($total, 2);
        $dp = round((float) $request->dp, 2);
        $metode = $request->metode_pembayaran;

        if ($metode === 'transfer' && $dp > $total) {
            return response()->json(['success' => false, 'message' => 'Transfer tidak boleh melebihi total.'], 422);
        }

        if ($dp >= $total) {
            $status = 'lunas';
            $bayar = $dp;
            $kembalian = $metode === 'tunai' ? round($dp - $total, 2) : 0;
            $kekurangan = 0;
        } else {
            $status = 'belum_lunas';
            $bayar = $dp;
            $kembalian = 0;
            $kekurangan = round($total - $dp, 2);
        }

        $transaksi = DB::transaction(function () use ($request, $total, $bayar, $kembalian, $kekurangan, $status, $metode, $dp, $rows) {
            $prefix = 'STRK-' . date('Ymd') . '-';
            $last = KasirTransaksi::whereDate('tanggal', $request->tanggal)
                ->where('no_struk', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('no_struk', 'desc')
                ->value('no_struk');
            $next = 1;
            if ($last) {
                $parts = explode('-', $last);
                $next = (int) end($parts) + 1;
            }
            $noStruk = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

            $trx = KasirTransaksi::create([
                'no_struk' => $noStruk,
                'tanggal' => $request->tanggal,
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'metode_pembayaran' => $metode,
                'dp' => $dp,
                'kekurangan' => $kekurangan,
                'status_pembayaran' => $status,
                'nama_pembeli' => $request->nama_pembeli,
                'input_by' => auth()->id(),
            ]);

            foreach ($rows as $row) {
                KasirTransaksiDetail::create([
                    'kasir_transaction_id' => $trx->id,
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'harga' => $row['harga'],
                    'qty' => $row['qty'],
                    'subtotal' => $row['subtotal'],
                    'modal' => $row['modal'],
                ]);
            }

            KasirPayment::create([
                'kasir_transaction_id' => $trx->id,
                'tanggal' => $request->tanggal,
                'metode_pembayaran' => $metode,
                'jumlah' => $dp,
                'keterangan' => 'Pembayaran awal',
                'created_by' => auth()->id(),
            ]);

            return $trx;
        });

        return response()->json([
            'success' => true,
            'id' => $transaksi->id,
            'no_struk' => $transaksi->no_struk,
            'tanggal' => $transaksi->tanggal,
            'total' => $transaksi->total,
            'bayar' => $transaksi->bayar,
            'kembalian' => $transaksi->kembalian,
            'metode_pembayaran' => $transaksi->metode_pembayaran,
            'dp' => $transaksi->dp,
            'kekurangan' => $transaksi->kekurangan,
            'status_pembayaran' => $transaksi->status_pembayaran,
            'struk_url' => route('kasir.struk', $transaksi->id),
        ]);
    }

    public function updatePrices(Request $request, Product $product)
    {
        $data = $request->validate([
            'prices' => 'required|array|min:1',
            'prices.*.min_qty' => 'required|integer|min:1',
            'prices.*.harga' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($product, $data) {
            $product->prices()->delete();
            foreach ($data['prices'] as $i => $p) {
                $product->prices()->create([
                    'tier' => $i + 1,
                    'min_qty' => (int) $p['min_qty'],
                    'harga' => (float) $p['harga'],
                ]);
            }
        });

        $product->load('prices', 'group');
        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'barcode' => $product->barcode,
                'product_name' => $product->product_name,
                'group_id' => $product->group_id,
                'group_name' => $product->group?->group_name,
                'prices' => $product->prices->map(fn($pr) => [
                    'tier' => $pr->tier,
                    'min_qty' => $pr->min_qty,
                    'harga' => (float) $pr->harga,
                ])->values(),
            ],
        ]);
    }

    public function pelunasan(Request $request, $id)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:tunai,transfer',
            'jumlah' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $transaksi = KasirTransaksi::findOrFail($id);

        if ($transaksi->status_pembayaran === 'lunas' || $transaksi->kekurangan <= 0) {
            return redirect()->route('kasir.index')->with('error', 'Transaksi sudah lunas.');
        }

        $jumlah = round((float) $request->jumlah, 2);
        $metode = $request->metode_pembayaran;

        if ($metode === 'transfer' && $jumlah > $transaksi->kekurangan) {
            return redirect()->route('kasir.index')->with('error', 'Transfer tidak boleh melebihi kekurangan.');
        }

        DB::transaction(function () use ($transaksi, $jumlah, $metode, $request) {
            $kekuranganSebelumnya = $transaksi->kekurangan;
            $sisa = round($kekuranganSebelumnya - $jumlah, 2);

            KasirPayment::create([
                'kasir_transaction_id' => $transaksi->id,
                'tanggal' => now()->format('Y-m-d'),
                'metode_pembayaran' => $metode,
                'jumlah' => $jumlah,
                'keterangan' => $request->keterangan ?: 'Pelunasan',
                'created_by' => auth()->id(),
            ]);

            $transaksi->dp = round($transaksi->dp + $jumlah, 2);

            if ($sisa <= 0) {
                $transaksi->status_pembayaran = 'lunas';
                $transaksi->kekurangan = 0;
                $transaksi->kembalian = $metode === 'tunai' ? round($jumlah - $kekuranganSebelumnya, 2) : 0;
            } else {
                $transaksi->kekurangan = $sisa;
                $transaksi->kembalian = 0;
            }

            $transaksi->save();
        });

        return redirect()->route('kasir.index')->with('success', 'Pelunasan berhasil disimpan.');
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));
        $sort = $request->get('sort', 'tanggal');
        $order = strtolower($request->get('order', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowed = ['no_struk', 'tanggal', 'nama_pembeli', 'kasir', 'metode_pembayaran', 'status_pembayaran', 'total', 'dp', 'kekurangan', 'kembalian'];
        if (!in_array($sort, $allowed, true)) {
            $sort = 'tanggal';
        }

        $query = KasirTransaksi::with(['details', 'user']);

        if ($search) {
            $query->where('no_struk', 'like', "%{$search}%");
        }
        $query->whereBetween('tanggal', [$dari, $sampai]);

        if ($sort === 'kasir') {
            $query->leftJoin('users', 'kasir_transactions.user_id', '=', 'users.id')
                ->orderBy('users.name', $order)
                ->select('kasir_transactions.*');
        } else {
            $query->orderBy($sort, $order);
            if ($sort !== 'tanggal') {
                $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
            } elseif ($sort === 'tanggal') {
                $query->orderBy('id', 'desc');
            }
        }

        $transaksis = $query->paginate(15)->withQueryString();

        $totals = KasirTransaksi::whereBetween('tanggal', [$dari, $sampai])
            ->selectRaw('COUNT(*) as jumlah, SUM(total) as omzet')
            ->first();

        $totalsLunas = KasirTransaksi::whereBetween('tanggal', [$dari, $sampai])
            ->where('status_pembayaran', 'lunas')
            ->count();

        $totalsBelumLunas = KasirTransaksi::whereBetween('tanggal', [$dari, $sampai])
            ->where('status_pembayaran', 'belum_lunas')
            ->count();

        $totalsKembalian = KasirTransaksi::whereBetween('tanggal', [$dari, $sampai])
            ->sum('kembalian');

        return view('kasir.index', compact('transaksis', 'search', 'dari', 'sampai', 'sort', 'order', 'totals', 'totalsLunas', 'totalsBelumLunas', 'totalsKembalian'));
    }

    public function dashboard(Request $request)
    {
        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $summary = KasirTransaksi::whereBetween('tanggal', [$dari, $sampai])
            ->selectRaw('
                COUNT(*) as total_transaksi,
                COALESCE(SUM(total), 0) as omzet,
                COALESCE(SUM(kekurangan), 0) as kekurangan,
                COALESCE(SUM(dp), 0) as dibayar,
                COUNT(CASE WHEN status_pembayaran = \'lunas\' THEN 1 END) as lunas,
                COUNT(CASE WHEN status_pembayaran = \'belum_lunas\' THEN 1 END) as belum_lunas
            ')
            ->first();

        $profitResult = KasirTransaksiDetail::whereHas('transaksi', function ($q) use ($dari, $sampai) {
                $q->whereBetween('tanggal', [$dari, $sampai]);
            })
            ->selectRaw('COALESCE(SUM(subtotal - (modal * qty)), 0) as profit')
            ->first();
        $profit = (float) $profitResult->profit;

        $labels = [];
        $omzetPerHari = [];
        $profitPerHari = [];
        $cursor = Carbon::parse($dari);
        $end = Carbon::parse($sampai);
        while ($cursor->lte($end)) {
            $d = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('d/m');
            $omzetPerHari[] = (float) KasirTransaksi::whereDate('tanggal', $d)->sum('total');
            $profitPerHari[] = (float) KasirTransaksiDetail::whereHas('transaksi', function ($q) use ($d) {
                    $q->whereDate('tanggal', $d);
                })
                ->selectRaw('COALESCE(SUM(subtotal - (modal * qty)), 0) as profit')
                ->first()
                ->profit;
            $cursor->addDay();
        }

        return view('kasir.dashboard', compact('dari', 'sampai', 'summary', 'profit', 'labels', 'omzetPerHari', 'profitPerHari'));
    }

    public function edit($id)
    {
        $transaksi = KasirTransaksi::with(['details.product.prices'])->findOrFail($id);
        $products = Product::with('prices')->where('is_active', true)->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'product_name' => $p->product_name,
                'barcode' => $p->barcode,
                'modal' => (float) $p->modal,
                'prices' => $p->prices->map(fn($pr) => [
                    'tier' => $pr->tier,
                    'min_qty' => $pr->min_qty,
                    'harga' => (float) $pr->harga,
                ])->values(),
            ])->values();

        return response()->json([
            'transaksi' => [
                'id' => $transaksi->id,
                'no_struk' => $transaksi->no_struk,
                'tanggal' => $transaksi->tanggal,
                'nama_pembeli' => $transaksi->nama_pembeli,
                'metode_pembayaran' => $transaksi->metode_pembayaran,
                'status_pembayaran' => $transaksi->status_pembayaran,
                'dp' => (float) $transaksi->dp,
                'total' => (float) $transaksi->total,
                'kekurangan' => (float) $transaksi->kekurangan,
                'kembalian' => (float) $transaksi->kembalian,
                'details' => $transaksi->details->map(fn($d) => [
                    'id' => $d->id,
                    'product_id' => $d->product_id,
                    'product_name' => $d->product_name,
                    'harga' => (float) $d->harga,
                    'qty' => (int) $d->qty,
                    'subtotal' => (float) $d->subtotal,
                    'modal' => (float) $d->modal,
                    'prices' => $d->product?->prices->map(fn($pr) => [
                        'tier' => $pr->tier,
                        'min_qty' => $pr->min_qty,
                        'harga' => (float) $pr->harga,
                    ])->values() ?? [],
                ])->values(),
            ],
            'products' => $products,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer',
            'status_pembayaran' => 'required|in:lunas,belum_lunas',
            'dp' => 'required|numeric|min:0',
            'nama_pembeli' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
        ]);

        $transaksi = KasirTransaksi::findOrFail($id);

        $total = 0;
        $rows = [];
        foreach ($request->items as $item) {
            $product = Product::with('prices')->findOrFail($item['product_id']);
            $qty = (int) $item['qty'];
            $harga = (float) $item['harga'];
            $subtotal = round($harga * $qty, 2);
            $total += $subtotal;
            $rows[] = [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'harga' => $harga,
                'qty' => $qty,
                'subtotal' => $subtotal,
                'modal' => $product->modal ?? 0,
            ];
        }

        $total = round($total, 2);
        $dp = round((float) $request->dp, 2);
        $metode = $request->metode_pembayaran;

        if ($metode === 'transfer' && $dp > $total) {
            return response()->json(['success' => false, 'message' => 'Transfer tidak boleh melebihi total.'], 422);
        }

        if ($request->status_pembayaran === 'lunas') {
            $status = 'lunas';
            $kekurangan = 0;
            $kembalian = $metode === 'tunai' ? round($dp - $total, 2) : 0;
            if ($kembalian < 0) $kembalian = 0;
        } else {
            $status = 'belum_lunas';
            if ($dp >= $total) {
                $kekurangan = 0;
                $kembalian = $metode === 'tunai' ? round($dp - $total, 2) : 0;
                $status = 'lunas';
            } else {
                $kekurangan = round($total - $dp, 2);
                $kembalian = 0;
            }
        }

        DB::transaction(function () use ($transaksi, $request, $total, $dp, $kekurangan, $kembalian, $status, $metode, $rows) {
            $transaksi->update([
                'tanggal' => $request->tanggal,
                'total' => $total,
                'dp' => $dp,
                'kekurangan' => $kekurangan,
                'kembalian' => $kembalian,
                'metode_pembayaran' => $metode,
                'status_pembayaran' => $status,
                'nama_pembeli' => $request->nama_pembeli,
            ]);

            $transaksi->details()->delete();
            foreach ($rows as $row) {
                KasirTransaksiDetail::create([
                    'kasir_transaction_id' => $transaksi->id,
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'harga' => $row['harga'],
                    'qty' => $row['qty'],
                    'subtotal' => $row['subtotal'],
                    'modal' => $row['modal'],
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Transaksi berhasil diperbarui.']);
    }

    public function struk($id)
    {
        $transaksi = KasirTransaksi::with('details')->findOrFail($id);
        return view('kasir.struk', compact('transaksi'));
    }

    public function destroy($id)
    {
        $transaksi = KasirTransaksi::findOrFail($id);
        $transaksi->delete();
        return redirect()->route('kasir.index')->with('success', 'Transaksi kasir berhasil dihapus.');
    }
}
