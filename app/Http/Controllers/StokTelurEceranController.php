<?php

namespace App\Http\Controllers;

use App\Models\StokTelurEceran;
use App\Models\SortasiTelurDetail;
use Illuminate\Http\Request;

class StokTelurEceranController extends Controller
{
    private function stokTersedia($gudangId = null)
    {
        $masukQuery = SortasiTelurDetail::whereHas('sortasiTelur', function ($q) use ($gudangId) {
            if ($gudangId) $q->where('gudang_id', $gudangId);
        });
        $masuk = $masukQuery->sum('butir');

        $keluarB2B = \App\Models\StokTelurKeluar::when($gudangId, fn($q) => $q->where('gudang_id', $gudangId))->sum('jumlah_butir');
        $eceran = StokTelurEceran::when($gudangId, fn($q) => $q->where('gudang_id', $gudangId))->sum('jumlah_butir');

        return $masuk - $keluarB2B - $eceran;
    }

    private function petisTersedia($excludeId = null)
    {
        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->pluck('id');
        if ($gudangs->isEmpty()) {
            return collect();
        }

        $used = StokTelurEceran::whereNotNull('sortasi_telur_detail_id')
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->pluck('sortasi_telur_detail_id');

        return SortasiTelurDetail::with(['sortasiTelur.gudang', 'sortasiTelur.kandang'])
            ->whereHas('sortasiTelur', function ($q) use ($gudangs) {
                $q->whereIn('gudang_id', $gudangs);
            })
            ->whereNotIn('id', $used)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($p) {
                $s = $p->sortasiTelur;
                return (object) [
                    'id' => $p->id,
                    'gudang_id' => $s->gudang_id,
                    'gudang_nama' => $s->gudang->nama_gudang ?? '-',
                    'kandang_nama' => $s->kandang->nama_kandang ?? null,
                    'butir' => $p->butir,
                    'karpet' => $p->karpet,
                    'berat' => $p->berat,
                    'tgl_sortir' => $s->tanggal->format('d/m/Y'),
                    'tgl_raw' => $s->tanggal->format('Y-m-d'),
                ];
            });
    }

    public function create()
    {
        $petis = $this->petisTersedia();
        $petisByGudang = $petis->groupBy('gudang_nama');
        return view('telur.eceran.create', compact('petisByGudang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'peti_ids' => 'required|array|min:1',
            'peti_ids.*' => 'exists:sortasi_telur_detail,id',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $petiIds = $request->peti_ids;
        $alreadyUsed = StokTelurEceran::whereIn('sortasi_telur_detail_id', $petiIds)->pluck('sortasi_telur_detail_id');

        if ($alreadyUsed->isNotEmpty()) {
            return back()->with('error', 'Beberapa peti sudah dialokasikan: #' . $alreadyUsed->join(', #'))->withInput();
        }

        $petis = SortasiTelurDetail::with('sortasiTelur.gudang')->whereIn('id', $petiIds)->get();

        $tanggal = $request->tanggal;
        $baseCount = StokTelurEceran::whereDate('tanggal', $tanggal)->count();
        $noRefs = [];

        foreach ($petis as $i => $p) {
            $seq = $baseCount + $i + 1;
            $noRef = 'EC-' . date('Ymd', strtotime($tanggal)) . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
            $noRefs[] = $noRef;

            StokTelurEceran::create([
                'tanggal' => $tanggal,
                'gudang_id' => $p->sortasiTelur->gudang_id,
                'sortasi_telur_detail_id' => $p->id,
                'unit_jual' => 'peti',
                'jumlah_butir' => $p->butir,
                'karpet' => $p->karpet,
                'berat_kg' => $p->berat,
                'peti' => 1,
                'no_referensi' => $noRef,
                'keterangan' => $request->keterangan,
                'input_by' => auth()->id(),
            ]);
        }

        $totalButir = $petis->sum('butir');
        return redirect()->route('setoran.gudang')->with('success', "Alokasi eceran berhasil. {$petis->count()} peti, {$totalButir} butir. No: " . implode(', ', $noRefs));
    }

    public function edit($id)
    {
        $eceran = StokTelurEceran::with('sortasiDetail.sortasiTelur.gudang')->findOrFail($id);
        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();
        $stokByGudang = [];
        foreach ($gudangs as $g) {
            $stokByGudang[$g->id] = $this->stokTersedia($g->id);
        }
        $currentStok = $this->stokTersedia($eceran->gudang_id) + $eceran->jumlah_butir;
        $stokByGudang[$eceran->gudang_id] = $currentStok;

        $petis = $this->petisTersedia($eceran->id);

        return view('telur.eceran.edit', compact('eceran', 'gudangs', 'stokByGudang', 'petis'));
    }

    public function update(Request $request, $id)
    {
        $eceran = StokTelurEceran::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'gudang_id' => 'required|exists:gudang,id',
            'unit_jual' => 'required|in:kg,peti,karpet,butir',
            'jumlah_butir' => 'required|integer|min:1',
            'berat_kg' => 'required|numeric|min:0',
            'karpet' => 'nullable|integer|min:0',
            'peti' => 'nullable|integer|min:0',
            'sortasi_telur_detail_id' => 'nullable|exists:sortasi_telur_detail,id',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $petiId = $request->sortasi_telur_detail_id;
        if ($petiId && $petiId != $eceran->sortasi_telur_detail_id) {
            $alreadyUsed = StokTelurEceran::where('sortasi_telur_detail_id', $petiId)
                ->where('id', '!=', $eceran->id)->exists();
            if ($alreadyUsed) {
                return back()->with('error', 'Peti ini sudah dialokasikan.')->withInput();
            }
        }

        $stokTersedia = $this->stokTersedia($request->gudang_id) + $eceran->jumlah_butir;
        if ($request->jumlah_butir > $stokTersedia) {
            return back()->with('error', "Stok gudang tidak mencukupi. Stok tersedia: " . number_format($stokTersedia) . " butir.")->withInput();
        }

        $eceran->update($request->only([
            'tanggal', 'gudang_id', 'sortasi_telur_detail_id', 'unit_jual',
            'jumlah_butir', 'berat_kg', 'karpet', 'peti', 'keterangan',
        ]));

        return redirect()->route('setoran.gudang')->with('success', 'Alokasi eceran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $eceran = StokTelurEceran::findOrFail($id);
        if ($eceran->transaksis()->count() > 0) {
            return back()->with('error', 'Alokasi ini sudah memiliki transaksi eceran. Hapus transaksinya terlebih dahulu.');
        }
        $eceran->delete();
        return redirect()->route('setoran.gudang')->with('success', 'Alokasi eceran berhasil dihapus.');
    }
}
