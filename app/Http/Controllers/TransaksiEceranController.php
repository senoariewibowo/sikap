<?php

namespace App\Http\Controllers;

use App\Models\StokTelurEceran;
use App\Models\TransaksiEceran;
use App\Models\TransaksiEceranDetail;
use App\Models\HargaTelur;
use Illuminate\Http\Request;

class TransaksiEceranController extends Controller
{
    private function poolStok($gudangIds = null): int
    {
        $query = StokTelurEceran::query();
        if ($gudangIds) $query->whereIn('gudang_id', $gudangIds);

        $totalAllocated = $query->sum('jumlah_butir');
        $totalSold = TransaksiEceranDetail::whereIn('stok_telur_eceran_id', fn($q) => $q->select('id')->from('stok_telur_eceran')
            ->when($gudangIds, fn($sq) => $sq->whereIn('gudang_id', $gudangIds))
        )->sum('jumlah_butir');

        return max(0, $totalAllocated - $totalSold);
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));
        $sort = $request->get('sort', 'tanggal');
        $order = $request->get('order', 'desc');
        $allowedSorts = ['tanggal', 'total_butir', 'harga_per_butir', 'total_harga'];
        if (!in_array($sort, $allowedSorts)) $sort = 'tanggal';
        if (!in_array($order, ['asc', 'desc'])) $order = 'desc';

        $query = TransaksiEceran::with(['details.stokEceran.gudang', 'user'])
            ->orderBy($sort, $order)->orderBy('id', 'desc');

        if ($search) {
            $query->where('keterangan', 'like', "%{$search}%");
        }
        $query->whereBetween('tanggal', [$dari, $sampai]);

        $transaksis = $query->paginate(15)->withQueryString();

        return view('eceran.transaksi.index', compact('transaksis', 'search', 'dari', 'sampai', 'sort', 'order'));
    }

    public function create()
    {
        $gudangIds = $this->getUserGudangIds();
        if (!auth()->user()->hasRole('super_admin') && empty($gudangIds)) {
            $gudangIds = [0];
        }

        $pool = $this->poolStok($gudangIds);
        $tanggal = now()->format('Y-m-d');
        $hargaButir = HargaTelur::hargaEceran('per_butir', $tanggal);
        $hargaKg = HargaTelur::hargaEceran('per_kg', $tanggal);
        $hargaKarpet = HargaTelur::hargaEceran('per_karpet', $tanggal);

        return view('eceran.transaksi.create', [
            'pool' => $pool,
            'hargaButir' => $hargaButir ? $hargaButir->harga : 0,
            'hargaKg' => $hargaKg ? $hargaKg->harga : 0,
            'hargaKarpet' => $hargaKarpet ? $hargaKarpet->harga : 0,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'tanggal' => 'required|date',
            'satuan' => 'required|in:per_butir,per_kg,per_karpet',
            'jumlah_butir' => 'required|integer|min:1',
            'harga_per_butir' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ];
        if ($request->satuan === 'per_kg') {
            $rules['berat_kg'] = 'required|numeric|min:0.01';
        } elseif ($request->satuan === 'per_karpet') {
            $rules['karpet'] = 'required|integer|min:1';
        }
        $request->validate($rules);

        $gudangIds = $this->getUserGudangIds();
        if (!auth()->user()->hasRole('super_admin') && empty($gudangIds)) {
            $gudangIds = [0];
        }

        $jumlahButir = (int) $request->jumlah_butir;
        $beratKg = $request->satuan === 'per_kg' ? (float) $request->berat_kg : null;
        $karpet = $request->satuan === 'per_karpet' ? (int) $request->karpet : null;

        $pool = $this->poolStok($gudangIds);
        if ($jumlahButir > $pool) {
            return back()->with('error', "Stok eceran tidak mencukupi. Stok tersedia: " . number_format($pool) . " butir.")->withInput();
        }

        if ($request->satuan === 'per_butir') {
            $totalHarga = $jumlahButir * $request->harga_per_butir;
        } elseif ($request->satuan === 'per_kg') {
            $totalHarga = $beratKg * $request->harga_per_butir;
        } else {
            $totalHarga = $karpet * $request->harga_per_butir;
        }

        $transaksi = TransaksiEceran::create([
            'tanggal' => $request->tanggal,
            'total_butir' => $jumlahButir,
            'satuan' => $request->satuan,
            'berat_kg' => $beratKg,
            'karpet' => $karpet,
            'harga_per_butir' => $request->harga_per_butir,
            'total_harga' => $totalHarga,
            'keterangan' => $request->keterangan,
            'input_by' => auth()->id(),
        ]);

        $sisa = $jumlahButir;
        $alokasis = StokTelurEceran::whereIn('gudang_id', $gudangIds)
            ->orderBy('tanggal')->orderBy('id')
            ->get();

        foreach ($alokasis as $a) {
            if ($sisa <= 0) break;
            $terjual = TransaksiEceranDetail::where('stok_telur_eceran_id', $a->id)->sum('jumlah_butir');
            $available = $a->jumlah_butir - $terjual;
            if ($available <= 0) continue;
            $ambil = min($sisa, $available);
            $transaksi->details()->create(['stok_telur_eceran_id' => $a->id, 'jumlah_butir' => $ambil]);
            $sisa -= $ambil;
        }

        $label = $request->satuan === 'per_butir' ? "{$jumlahButir} butir" : ($request->satuan === 'per_kg' ? number_format($beratKg, 2) . " kg" : "{$karpet} karpet");
        return redirect()->route('eceran.transaksi.index')->with('success', "Transaksi eceran berhasil. {$label}, {$jumlahButir} butir. Total: Rp " . number_format($totalHarga, 0, ',', '.'));
    }

    public function edit($id)
    {
        $transaksi = TransaksiEceran::with(['details.stokEceran.gudang'])->findOrFail($id);
        $gudangIds = $this->getUserGudangIds();
        if (!auth()->user()->hasRole('super_admin') && empty($gudangIds)) {
            $gudangIds = [0];
        }

        $pool = $this->poolStok($gudangIds) + $transaksi->total_butir;
        $tanggal = now()->format('Y-m-d');
        $hargaButir = HargaTelur::hargaEceran('per_butir', $tanggal);
        $hargaKg = HargaTelur::hargaEceran('per_kg', $tanggal);
        $hargaKarpet = HargaTelur::hargaEceran('per_karpet', $tanggal);

        return view('eceran.transaksi.edit', [
            'transaksi' => $transaksi,
            'pool' => $pool,
            'hargaButir' => $hargaButir ? $hargaButir->harga : 0,
            'hargaKg' => $hargaKg ? $hargaKg->harga : 0,
            'hargaKarpet' => $hargaKarpet ? $hargaKarpet->harga : 0,
        ]);
    }

    public function update(Request $request, $id)
    {
        $transaksi = TransaksiEceran::with('details')->findOrFail($id);

        $rules = [
            'tanggal' => 'required|date',
            'satuan' => 'required|in:per_butir,per_kg,per_karpet',
            'jumlah_butir' => 'required|integer|min:1',
            'harga_per_butir' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ];
        if ($request->satuan === 'per_kg') {
            $rules['berat_kg'] = 'required|numeric|min:0.01';
        } elseif ($request->satuan === 'per_karpet') {
            $rules['karpet'] = 'required|integer|min:1';
        }
        $request->validate($rules);

        $gudangIds = $this->getUserGudangIds();
        if (!auth()->user()->hasRole('super_admin') && empty($gudangIds)) {
            $gudangIds = [0];
        }

        $jumlahButir = (int) $request->jumlah_butir;
        $beratKg = $request->satuan === 'per_kg' ? (float) $request->berat_kg : null;
        $karpet = $request->satuan === 'per_karpet' ? (int) $request->karpet : null;

        $pool = $this->poolStok($gudangIds) + $transaksi->total_butir;
        if ($jumlahButir > $pool) {
            return back()->with('error', "Stok eceran tidak mencukupi. Stok tersedia: " . number_format($pool - $transaksi->total_butir) . " butir.")->withInput();
        }

        $transaksi->details()->delete();
        $sisa = $jumlahButir;
        $alokasis = StokTelurEceran::whereIn('gudang_id', $gudangIds)
            ->orderBy('tanggal')->orderBy('id')
            ->get();

        foreach ($alokasis as $a) {
            if ($sisa <= 0) break;
            $terjual = TransaksiEceranDetail::where('stok_telur_eceran_id', $a->id)->sum('jumlah_butir');
            $available = $a->jumlah_butir - $terjual;
            if ($available <= 0) continue;
            $ambil = min($sisa, $available);
            $transaksi->details()->create(['stok_telur_eceran_id' => $a->id, 'jumlah_butir' => $ambil]);
            $sisa -= $ambil;
        }

        if ($request->satuan === 'per_butir') {
            $totalHarga = $jumlahButir * $request->harga_per_butir;
        } elseif ($request->satuan === 'per_kg') {
            $totalHarga = $beratKg * $request->harga_per_butir;
        } else {
            $totalHarga = $karpet * $request->harga_per_butir;
        }

        $transaksi->update([
            'tanggal' => $request->tanggal,
            'total_butir' => $jumlahButir,
            'satuan' => $request->satuan,
            'berat_kg' => $beratKg,
            'karpet' => $karpet,
            'harga_per_butir' => $request->harga_per_butir,
            'total_harga' => $totalHarga,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('eceran.transaksi.index')->with('success', 'Transaksi eceran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $transaksi = TransaksiEceran::findOrFail($id);
        $transaksi->delete();
        return redirect()->route('eceran.transaksi.index')->with('success', 'Transaksi eceran berhasil dihapus.');
    }
}
