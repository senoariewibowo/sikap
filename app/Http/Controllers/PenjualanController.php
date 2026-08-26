<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPenjualan;
use App\Models\StokTelurKeluar;
use App\Models\StokTelurKeluarDetail;
use App\Models\PenjualanStok;
use App\Models\Customer;
use App\Models\HargaTelur;
use App\Models\Kandang;
use App\Models\ProduksiTelur;
use App\Exports\PenjualanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $customerId = $request->get('customer_id');
        $statusBayar = $request->get('status_pembayaran');
        $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $query = TransaksiPenjualan::with(['customer', 'stokTelurKeluar', 'user', 'stokDetails.stokTelurKeluar.gudang'])
            ->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
        if ($customerId) $query->where('customer_id', $customerId);
        if ($statusBayar) $query->where('status_pembayaran', $statusBayar);
        $query->whereBetween('tanggal', [$dari, $sampai]);
        $transaksis = $query->paginate(15)->withQueryString();
        $customers = Customer::orderBy('nama_customer')->get();

        $omzet = TransaksiPenjualan::whereBetween('tanggal', [$dari, $sampai])->sum('total_harga');
        $totalButir = TransaksiPenjualan::whereBetween('tanggal', [$dari, $sampai])->sum('jumlah_butir');
        $totalPeti = TransaksiPenjualan::whereBetween('tanggal', [$dari, $sampai])->sum('jumlah_satuan');
        $totalDp = TransaksiPenjualan::whereBetween('tanggal', [$dari, $sampai])->sum('dp');
        $piutang = TransaksiPenjualan::where('status_pembayaran', 'belum_lunas')
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->selectRaw('SUM(total_harga - dp) as sisa')->value('sisa') ?? 0;
        $omzetPerCustomer = TransaksiPenjualan::selectRaw('customer_id, SUM(total_harga) as total, SUM(jumlah_butir) as butir, SUM(jumlah_satuan) as peti')
            ->whereBetween('tanggal', [$dari, $sampai])->groupBy('customer_id')->with('customer')->get();

        return view('penjualan.index', compact('transaksis', 'customers', 'customerId', 'statusBayar', 'dari', 'sampai', 'omzet', 'totalButir', 'totalPeti', 'totalDp', 'piutang', 'omzetPerCustomer'));
    }

    private function getDriverName(): ?string
    {
        $user = auth()->user();
        if ($user->hasRole('driver')) {
            return $user->karyawan?->nama;
        }
        return null;
    }

    public function create(Request $request)
    {
        $customers = Customer::where('status', 'aktif')->orderBy('nama_customer')->get();
        $driverName = $this->getDriverName();
        $selectedSj = null;
        $selectedSjId = $request->get('stok_telur_keluar_id');

        if ($selectedSjId) {
            $selectedSj = \App\Models\StokTelurKeluar::with('gudang')->find($selectedSjId);
            if (!$selectedSj) {
                return back()->with('error', 'Surat jalan tidak ditemukan.');
            }
            if ($driverName && $selectedSj->driver !== $driverName) {
                return back()->with('error', 'Surat jalan bukan milik Anda.');
            }
        }

        if ($driverName) {
            $latestSj = \App\Models\StokTelurKeluar::where('driver', $driverName)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first();
            if (!$selectedSjId && $latestSj) {
                return redirect()->route('penjualan.create', ['stok_telur_keluar_id' => $latestSj->id]);
            }
            if ($selectedSjId && $latestSj && $selectedSjId != $latestSj->id) {
                return redirect()->route('penjualan.create', ['stok_telur_keluar_id' => $latestSj->id])
                    ->with('error', 'Penjualan hanya boleh dari surat jalan terbaru.');
            }
        }

        $details = StokTelurKeluarDetail::with(['stokTelurKeluar.gudang', 'sortasiTelurDetail'])
            ->whereNull('carried_over_to_id')
            ->whereHas('stokTelurKeluar', function ($q) use ($driverName, $selectedSjId) {
                if ($driverName) {
                    $q->where('driver', $driverName);
                }
                if ($selectedSjId) {
                    $q->where('id', $selectedSjId);
                }
            })
            ->orderBy('id', 'desc')
            ->get();

        $soldByDetail = PenjualanStok::whereNotNull('stok_telur_keluar_detail_id')
            ->selectRaw('stok_telur_keluar_detail_id, SUM(jumlah_butir) as terjual')
            ->groupBy('stok_telur_keluar_detail_id')
            ->pluck('terjual', 'stok_telur_keluar_detail_id');

        foreach ($details as $d) {
            $d->sisa = max(0, $d->jumlah_butir - ($soldByDetail[$d->id] ?? 0));
        }

        $stokDetail = $details->where('sisa', '>', 0)
            ->groupBy(fn($d) => $d->stokTelurKeluar->gudang->nama_gudang ?? 'Tanpa Gudang')
            ->map(fn($group) => $group->groupBy('stok_telur_keluar_id'));

        $totalGudang = [
            'butir' => $details->sum('sisa'),
            'berat' => round($details->where('sisa', '>', 0)->sum(fn($d) => $d->berat_kg), 1),
            'peti' => $details->where('sisa', '>', 0)->count(),
        ];

        $prices = [];
        foreach ($customers as $c) {
            $harga = HargaTelur::hargaBerlaku($c->id, 'per_peti', now()->format('Y-m-d'));
            $prices[$c->id] = $harga?->harga;
        }
        $hargaUmum = HargaTelur::hargaBerlaku(null, 'per_peti', now()->format('Y-m-d'));
        $prices[0] = $hargaUmum?->harga;

        return view('penjualan.create', compact('customers', 'totalGudang', 'stokDetail', 'prices', 'driverName', 'selectedSj'));
    }

    public function store(Request $request)
    {
        $rules = [
            'customer_id' => 'nullable|exists:customer,id',
            'customer_nama_baru' => 'nullable|string|max:255',
            'customer_alamat_baru' => 'nullable|string|max:200',
            'tanggal' => 'required|date',
            'harga_per_satuan' => 'required|numeric|min:0',
            'status_pembayaran' => 'required|in:lunas,belum_lunas',
            'metode_pembayaran' => 'required|in:tunai,transfer',
            'catatan_pembayaran' => 'nullable|string|max:500',
        ];
        if ($request->status_pembayaran === 'belum_lunas') $rules['dp'] = 'required|numeric|min:0';
        $request->validate($rules);

        if (!$request->customer_id && !$request->customer_nama_baru) {
            return back()->with('error', 'Pilih customer atau isi nama customer baru.')->withInput();
        }

        $customerId = $request->customer_id;
        if ($request->customer_nama_baru) {
            $customer = Customer::create([
                'nama_customer' => $request->customer_nama_baru,
                'tipe_customer' => 'retail',
                'alamat' => $request->customer_alamat_baru,
                'status' => 'aktif',
            ]);
            $customerId = $customer->id;
        }

        $request->validate(['detail_qty' => 'required|array|min:1', 'detail_qty.*' => 'nullable|integer|in:0,1']);
        $detailIds = collect($request->detail_qty)->filter(fn($q) => (int)$q === 1)->keys()->all();
        if (empty($detailIds)) return back()->with('error', 'Pilih minimal satu peti.')->withInput();

        $driverName = $this->getDriverName();
        $details = StokTelurKeluarDetail::with('stokTelurKeluar')
            ->whereIn('id', $detailIds)
            ->when($driverName, function ($q) use ($driverName) {
                $q->whereHas('stokTelurKeluar', fn($sq) => $sq->where('driver', $driverName));
            })
            ->get()
            ->keyBy('id');

        if ($details->count() !== count($detailIds)) {
            return back()->with('error', 'Peti yang dipilih tidak valid atau bukan milik Anda.')->withInput();
        }

        if ($driverName) {
            $latestSj = StokTelurKeluar::where('driver', $driverName)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first();
            $sjIds = $details->pluck('stok_telur_keluar_id')->unique();
            if ($latestSj && ($sjIds->count() !== 1 || $sjIds->first() !== $latestSj->id)) {
                return back()->with('error', 'Penjualan hanya boleh dari surat jalan terbaru.')->withInput();
            }
        }

        $soldByDetail = PenjualanStok::whereNotNull('stok_telur_keluar_detail_id')
            ->selectRaw('stok_telur_keluar_detail_id, SUM(jumlah_butir) as terjual')
            ->groupBy('stok_telur_keluar_detail_id')
            ->pluck('terjual', 'stok_telur_keluar_detail_id');

        $allocations = [];
        $jumlahSatuan = 0;
        foreach ($detailIds as $detailId) {
            $detail = $details[$detailId];
            $sisa = max(0, $detail->jumlah_butir - ($soldByDetail[$detail->id] ?? 0));
            if ($sisa < $detail->jumlah_butir) {
                return back()->with('error', "Peti {$detail->stokTelurKeluar->no_referensi} sudah terjual sebagian. Penjualan hanya boleh per peti penuh.")->withInput();
            }
            $allocations[] = [
                'stok_telur_keluar_id' => $detail->stok_telur_keluar_id,
                'stok_telur_keluar_detail_id' => $detail->id,
                'jumlah_butir' => $detail->jumlah_butir,
                'berat_kg' => $detail->berat_kg,
            ];
            $jumlahSatuan += 1;
        }

        $jumlahButir = array_sum(array_column($allocations, 'jumlah_butir'));
        $totalBerat = round(array_sum(array_column($allocations, 'berat_kg')), 2);
        $total = $request->harga_per_satuan * $jumlahSatuan;
        $dp = $request->status_pembayaran === 'lunas' ? $total : ($request->dp ?? 0);
        if ($dp > $total) return back()->with('error', 'DP tidak boleh melebihi total transaksi.')->withInput();

        $count = TransaksiPenjualan::whereDate('tanggal', $request->tanggal)->count() + 1;
        $noInv = 'INV-' . date('Ymd', strtotime($request->tanggal)) . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $transaksi = TransaksiPenjualan::create([
            'tanggal' => $request->tanggal,
            'customer_id' => $customerId,
            'stok_telur_keluar_id' => $allocations[0]['stok_telur_keluar_id'],
            'satuan' => 'per_peti',
            'jumlah_satuan' => $jumlahSatuan,
            'jumlah_butir' => $jumlahButir,
            'berat_kg' => $totalBerat,
            'harga_per_satuan' => $request->harga_per_satuan,
            'total_harga' => $total,
            'dp' => $dp,
            'status_pembayaran' => $request->status_pembayaran,
            'metode_pembayaran' => $request->metode_pembayaran,
            'catatan_pembayaran' => $request->catatan_pembayaran,
            'no_invoice' => $noInv,
            'input_by' => auth()->id(),
        ]);

        foreach ($allocations as $a) {
            PenjualanStok::create([
                'transaksi_penjualan_id' => $transaksi->id,
                'stok_telur_keluar_id' => $a['stok_telur_keluar_id'],
                'stok_telur_keluar_detail_id' => $a['stok_telur_keluar_detail_id'],
                'jumlah_butir' => $a['jumlah_butir'],
                'berat_kg' => $a['berat_kg'],
            ]);
        }

        $details->pluck('stok_telur_keluar_id')->unique()->each(function ($sjId) {
            StokTelurKeluar::find($sjId)?->recalcCarryoverFlag();
        });

        return redirect()->route('penjualan.index')->with('success', 'Transaksi penjualan berhasil dibuat. No: ' . $noInv);
    }

    public function edit(TransaksiPenjualan $transaksi)
    {
        if ($transaksi->ttd_petugas) {
            return redirect()->route('penjualan.index')->with('error', 'Transaksi sudah ditandatangani, tidak bisa diedit.');
        }

        $customers = Customer::where('status', 'aktif')->orderBy('nama_customer')->get();
        $driverName = $this->getDriverName();
        $selectedDetailIds = $transaksi->stokDetails->pluck('stok_telur_keluar_detail_id')->toArray();

        $details = StokTelurKeluarDetail::with(['stokTelurKeluar.gudang', 'sortasiTelurDetail'])
            ->whereHas('stokTelurKeluar', function ($q) use ($driverName) {
                if ($driverName) {
                    $q->where('driver', $driverName);
                }
            })
            ->where(function ($q) use ($selectedDetailIds) {
                $q->where(function ($q2) use ($selectedDetailIds) {
                    $q2->whereNull('carried_over_to_id');
                    $q2->whereDoesntHave('penjualanStok');
                });
                $q->orWhereIn('id', $selectedDetailIds);
            })
            ->orderBy('id', 'desc')
            ->get();

        $soldByDetail = PenjualanStok::whereNotNull('stok_telur_keluar_detail_id')
            ->where('transaksi_penjualan_id', '!=', $transaksi->id)
            ->selectRaw('stok_telur_keluar_detail_id, SUM(jumlah_butir) as terjual')
            ->groupBy('stok_telur_keluar_detail_id')
            ->pluck('terjual', 'stok_telur_keluar_detail_id');

        foreach ($details as $d) {
            $d->sisa = max(0, $d->jumlah_butir - ($soldByDetail[$d->id] ?? 0));
            $d->selected = in_array($d->id, $selectedDetailIds);
        }

        $stokDetail = $details->where(fn($d) => $d->sisa > 0 || $d->selected)
            ->groupBy(fn($d) => $d->stokTelurKeluar->gudang->nama_gudang ?? 'Tanpa Gudang')
            ->map(fn($group) => $group->groupBy('stok_telur_keluar_id'));

        $totalGudang = [
            'butir' => $details->where(fn($d) => $d->sisa > 0 || $d->selected)->sum('sisa'),
            'berat' => round($details->where(fn($d) => $d->sisa > 0 || $d->selected)->sum(fn($d) => $d->berat_kg), 1),
            'peti' => $details->where(fn($d) => $d->sisa > 0 || $d->selected)->count(),
        ];

        $prices = [];
        foreach ($customers as $c) {
            $prices[$c->id] = HargaTelur::hargaBerlaku($c->id, 'per_peti', $transaksi->tanggal->format('Y-m-d'))?->harga;
        }
        $prices[0] = HargaTelur::hargaBerlaku(null, 'per_peti', $transaksi->tanggal->format('Y-m-d'))?->harga;

        return view('penjualan.edit', compact('transaksi', 'customers', 'totalGudang', 'stokDetail', 'prices', 'driverName'));
    }

    public function update(Request $request, TransaksiPenjualan $transaksi)
    {
        if ($transaksi->ttd_petugas) {
            return back()->with('error', 'Transaksi sudah ditandatangani, tidak bisa diedit.');
        }

        $rules = [
            'customer_id' => 'nullable|exists:customer,id',
            'customer_nama_baru' => 'nullable|string|max:255',
            'customer_alamat_baru' => 'nullable|string|max:200',
            'tanggal' => 'required|date',
            'harga_per_satuan' => 'required|numeric|min:0',
            'status_pembayaran' => 'required|in:lunas,belum_lunas',
            'metode_pembayaran' => 'required|in:tunai,transfer',
            'catatan_pembayaran' => 'nullable|string|max:500',
        ];
        if ($request->status_pembayaran === 'belum_lunas') $rules['dp'] = 'required|numeric|min:0';
        $request->validate($rules);

        if (!$request->customer_id && !$request->customer_nama_baru) {
            return back()->with('error', 'Pilih customer atau isi nama customer baru.')->withInput();
        }

        $customerId = $request->customer_id;
        if ($request->customer_nama_baru) {
            $customer = Customer::create([
                'nama_customer' => $request->customer_nama_baru,
                'tipe_customer' => 'retail',
                'alamat' => $request->customer_alamat_baru,
                'status' => 'aktif',
            ]);
            $customerId = $customer->id;
        }

        $request->validate(['detail_qty' => 'required|array|min:1', 'detail_qty.*' => 'nullable|integer|in:0,1']);
        $detailIds = collect($request->detail_qty)->filter(fn($q) => (int)$q === 1)->keys()->all();
        if (empty($detailIds)) return back()->with('error', 'Pilih minimal satu peti.')->withInput();

        $driverName = $this->getDriverName();
        $details = StokTelurKeluarDetail::with('stokTelurKeluar')
            ->whereIn('id', $detailIds)
            ->when($driverName, function ($q) use ($driverName) {
                $q->whereHas('stokTelurKeluar', fn($sq) => $sq->where('driver', $driverName));
            })
            ->get()
            ->keyBy('id');

        if ($details->count() !== count($detailIds)) {
            return back()->with('error', 'Peti yang dipilih tidak valid atau bukan milik Anda.')->withInput();
        }

        if ($driverName) {
            $latestSj = StokTelurKeluar::where('driver', $driverName)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first();
            $existingDetailIds = $transaksi->stokDetails->pluck('stok_telur_keluar_detail_id');
            $newDetailIds = collect($detailIds)->diff($existingDetailIds);
            $newSjIds = $details->whereIn('id', $newDetailIds)->pluck('stok_telur_keluar_id')->unique();
            if ($latestSj && ($newSjIds->count() !== 1 || $newSjIds->first() !== $latestSj->id)) {
                return back()->with('error', 'Hanya boleh menambahkan peti dari surat jalan terbaru.')->withInput();
            }
        }

        $soldByDetail = PenjualanStok::whereNotNull('stok_telur_keluar_detail_id')
            ->where('transaksi_penjualan_id', '!=', $transaksi->id)
            ->selectRaw('stok_telur_keluar_detail_id, SUM(jumlah_butir) as terjual')
            ->groupBy('stok_telur_keluar_detail_id')
            ->pluck('terjual', 'stok_telur_keluar_detail_id');

        $allocations = [];
        $jumlahSatuan = 0;
        foreach ($detailIds as $detailId) {
            $detail = $details[$detailId];
            $sisa = max(0, $detail->jumlah_butir - ($soldByDetail[$detail->id] ?? 0));
            if ($sisa < $detail->jumlah_butir) {
                return back()->with('error', "Peti {$detail->stokTelurKeluar->no_referensi} sudah terjual sebagian. Penjualan hanya boleh per peti penuh.")->withInput();
            }
            $allocations[] = [
                'stok_telur_keluar_id' => $detail->stok_telur_keluar_id,
                'stok_telur_keluar_detail_id' => $detail->id,
                'jumlah_butir' => $detail->jumlah_butir,
                'berat_kg' => $detail->berat_kg,
            ];
            $jumlahSatuan += 1;
        }

        $jumlahButir = array_sum(array_column($allocations, 'jumlah_butir'));
        $totalBerat = round(array_sum(array_column($allocations, 'berat_kg')), 2);
        $total = $request->harga_per_satuan * $jumlahSatuan;
        $dp = $request->status_pembayaran === 'lunas' ? $total : ($request->dp ?? 0);
        if ($dp > $total) return back()->with('error', 'DP tidak boleh melebihi total transaksi.')->withInput();

        $oldSjIds = $transaksi->stokDetails->pluck('stok_telur_keluar_id')->unique();

        $transaksi->update([
            'tanggal' => $request->tanggal,
            'customer_id' => $customerId,
            'stok_telur_keluar_id' => $allocations[0]['stok_telur_keluar_id'],
            'satuan' => 'per_peti',
            'jumlah_satuan' => $jumlahSatuan,
            'jumlah_butir' => $jumlahButir,
            'berat_kg' => $totalBerat,
            'harga_per_satuan' => $request->harga_per_satuan,
            'total_harga' => $total,
            'dp' => $dp,
            'status_pembayaran' => $request->status_pembayaran,
            'metode_pembayaran' => $request->metode_pembayaran,
            'catatan_pembayaran' => $request->catatan_pembayaran,
        ]);

        $transaksi->stokDetails()->delete();
        foreach ($allocations as $a) {
            PenjualanStok::create([
                'transaksi_penjualan_id' => $transaksi->id,
                'stok_telur_keluar_id' => $a['stok_telur_keluar_id'],
                'stok_telur_keluar_detail_id' => $a['stok_telur_keluar_detail_id'],
                'jumlah_butir' => $a['jumlah_butir'],
                'berat_kg' => $a['berat_kg'],
            ]);
        }

        $oldSjIds->merge($details->pluck('stok_telur_keluar_id'))->unique()->each(function ($sjId) {
            StokTelurKeluar::find($sjId)?->recalcCarryoverFlag();
        });

        return redirect()->route('penjualan.index')->with('success', 'Transaksi penjualan berhasil diperbarui. No: ' . $transaksi->no_invoice);
    }

    public function show(TransaksiPenjualan $transaksi) { $transaksi->load(['customer', 'stokTelurKeluar.gudang', 'user', 'stokDetails.stokTelurKeluar.gudang']); return view('penjualan.show', compact('transaksi')); }
    public function invoice(TransaksiPenjualan $transaksi) { $transaksi->load(['customer', 'stokTelurKeluar.gudang', 'user', 'ttdPetugas', 'stokDetails.stokTelurKeluar.gudang']); return view('penjualan.invoice', compact('transaksi')); }

    public function downloadInvoice(TransaksiPenjualan $transaksi)
    {
        $transaksi->load(['customer', 'stokTelurKeluar.gudang', 'user', 'ttdPetugas', 'stokDetails.stokTelurKeluar.gudang']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penjualan.invoice-pdf', compact('transaksi'));
        return $pdf->download('Invoice-' . $transaksi->no_invoice . '.pdf');
    }

    public function ttdInvoice(Request $request, TransaksiPenjualan $transaksi)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['petugas_kandang', 'super_admin', 'driver'])) abort(403);

        if ($transaksi->ttd_petugas && !$user->hasRole('super_admin')) {
            return back()->with('error', 'TTD sudah terisi.');
        }

        $update = ['ttd_petugas' => $user->id, 'ttd_petugas_at' => now()];
        $sig = $request->get('signature');
        if ($sig) {
            $update['ttd_petugas_img'] = $sig;
        }

        $transaksi->update($update);
        return back()->with('success', 'TTD berhasil.');
    }

    public function updatePembayaran(Request $request, TransaksiPenjualan $transaksi)
    {
        $request->validate([
            'status_pembayaran' => 'required|in:lunas,belum_lunas',
            'metode_pembayaran' => 'required|in:tunai,transfer',
            'catatan_pembayaran' => 'nullable|string|max:500',
        ]);
        $data = [
            'status_pembayaran' => $request->status_pembayaran,
            'metode_pembayaran' => $request->metode_pembayaran,
            'catatan_pembayaran' => $request->catatan_pembayaran,
        ];
        if ($request->status_pembayaran === 'lunas') $data['dp'] = $transaksi->total_harga;
        $transaksi->update($data);
        return back()->with('success', 'Status pembayaran diperbarui.');
    }

    public function destroy(TransaksiPenjualan $transaksi) {
        $sjIds = $transaksi->stokDetails->pluck('stok_telur_keluar_id')->unique();
        $transaksi->delete();
        $sjIds->each(function ($sjId) {
            StokTelurKeluar::find($sjId)?->recalcCarryoverFlag();
        });
        return redirect()->route('penjualan.index')->with('success', 'Transaksi dihapus.');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new PenjualanExport($request->customer_id, $request->dari ?? now()->subDays(30)->format('Y-m-d'), $request->sampai ?? now()->format('Y-m-d')), 'Laporan_Penjualan.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $customerId = $request->get('customer_id'); $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d')); $sampai = $request->get('sampai', now()->format('Y-m-d'));
        $data = TransaksiPenjualan::with(['customer', 'stokTelurKeluar'])->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->whereBetween('tanggal', [$dari, $sampai])->orderBy('tanggal')->get();
        $pdf = PDF::loadView('penjualan.pdf', compact('data', 'dari', 'sampai')); $pdf->setPaper('A4', 'landscape');
        return $pdf->download("Laporan_Penjualan_{$dari}_{$sampai}.pdf");
    }
}
