<?php

namespace App\Http\Controllers;

use App\Models\PakanPemakaian;
use App\Models\PakanDistribusi;
use App\Models\Pakan;
use App\Models\Kandang;
use App\Http\Requests\StorePakanPemakaianRequest;
use Illuminate\Http\Request;

class PemakaianPakanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $kandangAktif = $user->karyawan?->kandangAktif()->get() ?? collect();
        $kandangId = $request->get('kandang_id', $kandangAktif->first()?->id);
        $kandangIds = $kandangAktif->pluck('id')->toArray();

        $distribusiPending = PakanDistribusi::with(['pakan', 'gudang', 'kandang'])
            ->where('status', 'dikirim')
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->when(!empty($kandangIds), fn($q) => $q->whereIn('kandang_id', $kandangIds))
            ->orderBy('tanggal_kirim', 'desc')
            ->get();

        $distribusiDiterima = PakanDistribusi::with(['pakan', 'gudang', 'kandang'])
            ->where('status', 'diterima')
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->when(!empty($kandangIds), fn($q) => $q->whereIn('kandang_id', $kandangIds))
            ->orderBy('tanggal_diterima', 'desc')
            ->get();

        $pemakaians = PakanPemakaian::with(['pakan', 'kandang', 'user'])
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->when(!empty($kandangIds), fn($q) => $q->whereIn('kandang_id', $kandangIds))
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)->withQueryString();

        $pakans = Pakan::where('status', 'aktif')->orderBy('nama')->get();

        return view('pakan.pemakaian.index', compact(
            'kandangAktif', 'kandangId',
            'distribusiPending', 'distribusiDiterima',
            'pemakaians', 'pakans'
        ));
    }

    public function store(StorePakanPemakaianRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $pakan = Pakan::find($request->pakan_id);
        $stokKandang = $pakan->stokKandang($request->kandang_id);

        if ($request->jumlah > $stokKandang) {
            return back()->withInput()->with('error', "Stok kandang tidak mencukupi. Tersedia: {$stokKandang} {$pakan->satuan}.");
        }

        PakanPemakaian::create($data);

        return redirect()->route('pakan.pemakaian.index', ['kandang_id' => $request->kandang_id])
            ->with('success', "Pemakaian {$pakan->nama} berhasil dicatat.");
    }

    public function terima(PakanDistribusi $distribusi)
    {
        if ($distribusi->status !== 'dikirim') {
            return back()->with('error', 'Distribusi sudah diterima sebelumnya.');
        }

        $user = auth()->user();
        $kandangAktif = $user->karyawan?->kandangAktif()->get()->pluck('id')->toArray() ?? [];

        if (!in_array($distribusi->kandang_id, $kandangAktif)) {
            return back()->with('error', 'Anda tidak ditugaskan di kandang ini.');
        }

        $distribusi->update([
            'status' => 'diterima',
            'diterima_oleh' => $user->id,
            'tanggal_diterima' => now()->format('Y-m-d'),
        ]);

        return back()->with('success', "Distribusi {$distribusi->pakan->nama} telah diterima.");
    }

    public function edit(PakanPemakaian $pemakaian)
    {
        $pakans = Pakan::where('status', 'aktif')->orderBy('nama')->get();
        $kandangAktif = auth()->user()->karyawan?->kandangAktif()->get() ?? collect();
        return view('pakan.pemakaian.edit', compact('pemakaian', 'pakans', 'kandangAktif'));
    }

    public function update(StorePakanPemakaianRequest $request, PakanPemakaian $pemakaian)
    {
        $data = $request->validated();

        $pakan = Pakan::find($request->pakan_id);
        $stokKandang = $pakan->stokKandang($request->kandang_id) + $pemakaian->jumlah;

        if ($request->jumlah > $stokKandang) {
            return back()->withInput()->with('error', "Stok kandang tidak mencukupi. Tersedia: {$stokKandang} {$pakan->satuan}.");
        }

        $pemakaian->update($data);

        return redirect()->route('pakan.pemakaian.index', ['kandang_id' => $request->kandang_id])
            ->with('success', "Pemakaian {$pakan->nama} berhasil diperbarui.");
    }

    public function destroy(PakanPemakaian $pemakaian)
    {
        $pemakaian->delete();
        return back()->with('success', 'Pemakaian berhasil dihapus.');
    }
}
