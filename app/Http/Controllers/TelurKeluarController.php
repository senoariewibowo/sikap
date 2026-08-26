<?php

namespace App\Http\Controllers;

use App\Models\StokTelurKeluar;
use App\Models\StokTelurKeluarDetail;
use App\Models\StokTelurEceran;
use App\Models\SortasiTelur;
use App\Models\SortasiTelurDetail;
use Illuminate\Http\Request;

class TelurKeluarController extends Controller
{
    private function stokTersedia($gudangId = null)
    {
        $masukQuery = SortasiTelurDetail::whereHas('sortasiTelur', function ($q) use ($gudangId) {
            if ($gudangId) $q->where('gudang_id', $gudangId);
        });
        $masuk = $masukQuery->sum('butir');

        $keluarCustomerQuery = StokTelurKeluarDetail::whereHas('stokTelurKeluar', function ($q) use ($gudangId) {
            if ($gudangId) $q->where('gudang_id', $gudangId);
        });
        $keluarCustomer = $keluarCustomerQuery->sum('jumlah_butir');

        $keluarEceran = StokTelurEceran::when($gudangId, fn($q) => $q->where('gudang_id', $gudangId))->sum('jumlah_butir');

        return $masuk - $keluarCustomer - $keluarEceran;
    }

    private function petisTersedia($excludeDetailIds = [])
    {
        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->pluck('id');
        if ($gudangs->isEmpty()) {
            return collect();
        }

        $usedKeluar = StokTelurKeluarDetail::when($excludeDetailIds, fn($q) => $q->whereNotIn('id', $excludeDetailIds))
            ->pluck('sortasi_telur_detail_id');
        $usedEceran = StokTelurEceran::whereNotNull('sortasi_telur_detail_id')->pluck('sortasi_telur_detail_id');

        $used = $usedKeluar->merge($usedEceran)->unique()->values();

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
                    'kode_peti' => $p->kode_peti ?? '-',
                    'butir' => $p->butir,
                    'karpet' => $p->karpet,
                    'berat' => $p->berat,
                    'tgl_sortir' => $s->tanggal->format('d/m/Y'),
                    'tgl_raw' => $s->tanggal->format('Y-m-d'),
                ];
            });
    }

    private function getCarryoverDetailsForDriver(string $driverName): array
    {
        $previousSJs = StokTelurKeluar::with(['details.sortasiTelurDetail.sortasiTelur.gudang', 'penjualanStokDetails'])
            ->where('driver', $driverName)
            ->where('carryover_sisa', true)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $carryover = [];
        foreach ($previousSJs as $sj) {
            foreach ($sj->details as $detail) {
                if ($detail->carried_over_to_id) continue;
                $sold = $sj->penjualanStokDetails->where('stok_telur_keluar_detail_id', $detail->id)->sum('jumlah_butir');
                $sisa = max(0, $detail->jumlah_butir - $sold);
                if ($sisa > 0) {
                    $carryover[] = [
                        'source_detail_id' => $detail->id,
                        'sortasi_telur_detail_id' => $detail->sortasi_telur_detail_id,
                        'jumlah_butir' => $sisa,
                        'berat_kg' => $detail->berat_kg,
                        'karpet' => $detail->karpet,
                        'peti' => 1,
                        'source_sj' => $sj,
                        'kode_peti' => $detail->sortasiTelurDetail->kode_peti ?? '-',
                        'gudang_nama' => $detail->sortasiTelurDetail->sortasiTelur->gudang->nama_gudang ?? '-',
                    ];
                }
            }
        }
        return $carryover;
    }

    public function index(Request $request)
    {
        $gudangId = $request->get('gudang_id');
        $search = $request->get('search');
        $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));
        $sort = $request->get('sort', 'tanggal');
        $order = $request->get('order', 'desc');
        $allowedSorts = ['tanggal', 'no_referensi', 'jumlah_butir', 'berat_kg'];
        if (!in_array($sort, $allowedSorts)) $sort = 'tanggal';
        if (!in_array($order, ['asc', 'desc'])) $order = 'desc';

        $query = StokTelurKeluar::with(['gudang', 'user', 'details.sortasiDetail.sortasiTelur.kandang', 'penjualanStokDetails'])
            ->orderBy($sort, $order)->orderBy('id', 'desc');

        if (!auth()->user()->hasRole('super_admin')) {
            $gudangIds = $this->getUserGudangIds();
            if (!empty($gudangIds)) {
                $query->whereIn('gudang_id', $gudangIds);
            }
        }
        if ($gudangId) $query->where('gudang_id', $gudangId);
        if ($search) $query->where(fn($q) => $q->where('no_referensi', 'like', "%{$search}%")
            ->orWhere('driver', 'like', "%{$search}%"));
        $query->whereBetween('tanggal', [$dari, $sampai]);
        $stoks = $query->paginate(15)->withQueryString();

        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();

        $masukAll = SortasiTelurDetail::whereHas('sortasiTelur', function ($q) use ($gudangId) {
            if ($gudangId) $q->where('gudang_id', $gudangId);
        })->sum('butir');

        $keluarAll = $gudangId
            ? (StokTelurKeluarDetail::whereHas('stokTelurKeluar', fn($q) => $q->where('gudang_id', $gudangId))->sum('jumlah_butir')
              + StokTelurEceran::where('gudang_id', $gudangId)->sum('jumlah_butir'))
            : (StokTelurKeluarDetail::sum('jumlah_butir') + StokTelurEceran::sum('jumlah_butir'));

        $petiMasukAll = SortasiTelurDetail::whereHas('sortasiTelur', function ($q) use ($gudangId) {
            if ($gudangId) $q->where('gudang_id', $gudangId);
        })->count();

        $petiKeluarAll = $gudangId
            ? (StokTelurKeluarDetail::whereHas('stokTelurKeluar', fn($q) => $q->where('gudang_id', $gudangId))->count()
              + StokTelurEceran::where('gudang_id', $gudangId)->sum('peti'))
            : (StokTelurKeluarDetail::count() + StokTelurEceran::sum('peti'));

        $petiSisaAll = max(0, $petiMasukAll - $petiKeluarAll);
        $petiKeluarPercentAll = $petiMasukAll > 0 ? round($petiKeluarAll / $petiMasukAll * 100, 1) : 0;

        $keluarButirSJ = StokTelurKeluar::when($gudangId, fn($q) => $q->where('gudang_id', $gudangId))->sum('jumlah_butir');
        $keluarPetiSJ = StokTelurKeluar::when($gudangId, fn($q) => $q->where('gudang_id', $gudangId))->sum('peti');

        $sortasiGudangs = SortasiTelur::with('detail')
            ->when($gudangId, fn($q) => $q->where('gudang_id', $gudangId))
            ->get()
            ->groupBy('gudang_id');

        $rekapGudang = $gudangs->map(function ($g) use ($sortasiGudangs) {
            $sortasiList = $sortasiGudangs->get($g->id, collect());
            $masuk = $sortasiList->flatMap->detail->sum('butir');
            $keluarSJ = StokTelurKeluar::where('gudang_id', $g->id)->sum('jumlah_butir');
            $eceranButir = StokTelurEceran::where('gudang_id', $g->id)->sum('jumlah_butir');
            $keluarTotal = $keluarSJ + $eceranButir;
            $sisa = $masuk - $keluarTotal;
            $percent = $masuk > 0 ? round($keluarTotal / $masuk * 100, 1) : 0;

            $petiMasuk = $sortasiList->flatMap->detail->count();
            $petiKeluarSJ = StokTelurKeluar::where('gudang_id', $g->id)->sum('peti');
            $eceranPeti = StokTelurEceran::where('gudang_id', $g->id)->sum('peti');
            $petiKeluarTotal = $petiKeluarSJ + $eceranPeti;
            $petiSisa = max(0, $petiMasuk - $petiKeluarTotal);
            $petiPercent = $petiMasuk > 0 ? round($petiKeluarTotal / $petiMasuk * 100, 1) : 0;

            return (object) [
                'gudang' => $g,
                'masuk' => $masuk,
                'keluar_sj' => $keluarSJ,
                'eceran_butir' => $eceranButir,
                'keluar_total' => $keluarTotal,
                'sisa' => $sisa,
                'percent' => $percent,
                'peti_masuk' => $petiMasuk,
                'peti_keluar_sj' => $petiKeluarSJ,
                'eceran_peti' => $eceranPeti,
                'peti_sisa' => $petiSisa,
                'peti_percent' => $petiPercent,
            ];
        });

        return view('telur.keluar.index', compact(
            'stoks', 'gudangs', 'gudangId', 'search', 'dari', 'sampai',
            'masukAll', 'keluarAll', 'rekapGudang', 'sort', 'order',
            'petiMasukAll', 'petiKeluarAll', 'petiSisaAll', 'petiKeluarPercentAll',
            'keluarButirSJ', 'keluarPetiSJ'
        ));
    }

    public function create()
    {
        $petis = $this->petisTersedia();
        $petisByGudang = $petis->groupBy('gudang_nama');
        $dapters = \App\Models\Karyawan::whereHas('user.role', fn($q) => $q->where('nama_role', 'driver'))
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama']);
        return view('telur.keluar.create', compact('petisByGudang', 'dapters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'driver' => 'nullable|string|max:255',
            'peti_ids' => 'required|array|min:1',
            'peti_ids.*' => 'exists:sortasi_telur_detail,id',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $petis = SortasiTelurDetail::with('sortasiTelur.gudang')->whereIn('id', $request->peti_ids)->get();

        if ($petis->isEmpty()) {
            return back()->with('error', 'Peti yang dipilih tidak valid.')->withInput();
        }

        $gudangIds = $petis->pluck('sortasiTelur.gudang_id')->unique();
        if ($gudangIds->count() > 1) {
            return back()->with('error', 'Peti yang dipilih harus dari gudang yang sama dalam 1 pengiriman.')->withInput();
        }
        $gudangId = $gudangIds->first();

        $alreadyUsed = StokTelurKeluarDetail::whereIn('sortasi_telur_detail_id', $petis->pluck('id'))->exists();
        if ($alreadyUsed) {
            return back()->with('error', 'Beberapa peti sudah dialokasikan.')->withInput();
        }

        $butirTotal = $petis->sum('butir');
        $stokTersedia = $this->stokTersedia($gudangId);
        if ($butirTotal > $stokTersedia) {
            return back()->with('error', "Stok tidak mencukupi. Stok tersedia: " . number_format($stokTersedia) . " butir.")->withInput();
        }

        $carryoverDetails = [];
        if ($request->driver) {
            $carryoverDetails = $this->getCarryoverDetailsForDriver($request->driver);
        }

        $carryoverButir = collect($carryoverDetails)->sum('jumlah_butir');
        $carryoverBerat = collect($carryoverDetails)->sum('berat_kg');
        $carryoverKarpet = collect($carryoverDetails)->sum('karpet');
        $carryoverPeti = collect($carryoverDetails)->sum('peti');

        $count = StokTelurKeluar::whereDate('tanggal', $request->tanggal)->count() + 1;
        $noRef = 'SJ-' . date('Ymd', strtotime($request->tanggal)) . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $stok = StokTelurKeluar::create([
            'tanggal' => $request->tanggal,
            'gudang_id' => $gudangId,
            'driver' => $request->driver,
            'unit_jual' => 'peti',
            'jumlah_butir' => $butirTotal + $carryoverButir,
            'berat_kg' => $petis->sum('berat') + $carryoverBerat,
            'karpet' => $petis->sum('karpet') + $carryoverKarpet,
            'peti' => $petis->count() + $carryoverPeti,
            'no_referensi' => $noRef,
            'keterangan' => $request->keterangan,
            'input_by' => auth()->id(),
        ]);

        foreach ($petis as $p) {
            $stok->details()->create([
                'sortasi_telur_detail_id' => $p->id,
                'jumlah_butir' => $p->butir,
                'berat_kg' => $p->berat,
                'karpet' => $p->karpet,
                'peti' => 1,
            ]);
        }

        foreach ($carryoverDetails as $c) {
            $stok->details()->create([
                'sortasi_telur_detail_id' => $c['sortasi_telur_detail_id'],
                'jumlah_butir' => $c['jumlah_butir'],
                'berat_kg' => $c['berat_kg'],
                'karpet' => $c['karpet'],
                'peti' => 1,
                'keterangan' => 'Carryover dari ' . ($c['source_sj']->no_referensi ?? 'SJ lama'),
            ]);
        }

        foreach ($carryoverDetails as $c) {
            if (!empty($c['source_detail_id'])) {
                StokTelurKeluarDetail::where('id', $c['source_detail_id'])->update(['carried_over_to_id' => $stok->id]);
            }
        }

        if ($request->driver) {
            StokTelurKeluar::where('driver', $request->driver)
                ->where('carryover_sisa', true)
                ->where('id', '!=', $stok->id)
                ->update(['carryover_sisa' => false]);
        }

        $stok->recalcCarryoverFlag();

        return redirect()->route('telur.keluar.index')->with('success', 'Telur keluar berhasil dicatat. No: ' . $noRef);
    }

    public function edit(StokTelurKeluar $stokTelur)
    {
        if ($stokTelur->penjualanStokDetails()->exists()) {
            return redirect()->route('telur.keluar.index')->with('error', 'Data sudah terjual, tidak bisa diedit.');
        }

        $currentDetailIds = $stokTelur->details->pluck('id')->toArray();
        $petis = $this->petisTersedia($currentDetailIds);

        $currentPetis = SortasiTelurDetail::with(['sortasiTelur.gudang', 'sortasiTelur.kandang'])
            ->whereIn('id', $stokTelur->details->pluck('sortasi_telur_detail_id'))
            ->get()
            ->map(function ($p) {
                $s = $p->sortasiTelur;
                return (object) [
                    'id' => $p->id,
                    'gudang_id' => $s->gudang_id,
                    'gudang_nama' => $s->gudang->nama_gudang ?? '-',
                    'kandang_nama' => $s->kandang->nama_kandang ?? null,
                    'kode_peti' => $p->kode_peti ?? '-',
                    'butir' => $p->butir,
                    'karpet' => $p->karpet,
                    'berat' => $p->berat,
                    'tgl_sortir' => $s->tanggal->format('d/m/Y'),
                    'tgl_raw' => $s->tanggal->format('Y-m-d'),
                    'selected' => true,
                ];
            });

        $allPetis = $petis->merge($currentPetis)->groupBy('gudang_nama');
        $dapters = \App\Models\Karyawan::whereHas('user.role', fn($q) => $q->where('nama_role', 'driver'))
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama']);
        return view('telur.keluar.edit', compact('stokTelur', 'allPetis', 'dapters'));
    }

    public function update(Request $request, StokTelurKeluar $stokTelur)
    {
        if ($stokTelur->penjualanStokDetails()->exists()) {
            return back()->with('error', 'Data sudah terjual, tidak bisa diedit.')->withInput();
        }

        $request->validate([
            'tanggal' => 'required|date',
            'driver' => 'nullable|string|max:255',
            'peti_ids' => 'required|array|min:1',
            'peti_ids.*' => 'exists:sortasi_telur_detail,id',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $petis = SortasiTelurDetail::with('sortasiTelur.gudang')->whereIn('id', $request->peti_ids)->get();

        if ($petis->isEmpty()) {
            return back()->with('error', 'Peti yang dipilih tidak valid.')->withInput();
        }

        $gudangIds = $petis->pluck('sortasiTelur.gudang_id')->unique();
        if ($gudangIds->count() > 1) {
            return back()->with('error', 'Peti yang dipilih harus dari gudang yang sama dalam 1 pengiriman.')->withInput();
        }
        $gudangId = $gudangIds->first();

        if ($gudangId != $stokTelur->gudang_id) {
            return back()->with('error', 'Gudang tidak bisa diubah. Silakan hapus data ini dan buat pengiriman baru.')->withInput();
        }

        $currentDetailIds = $stokTelur->details->pluck('id')->toArray();
        $alreadyUsed = StokTelurKeluarDetail::whereIn('sortasi_telur_detail_id', $petis->pluck('id'))
            ->whereNotIn('id', $currentDetailIds)
            ->exists();
        if ($alreadyUsed) {
            return back()->with('error', 'Beberapa peti sudah dialokasikan di pengiriman lain.')->withInput();
        }

        $butirTotal = $petis->sum('butir');
        $stokTersedia = $this->stokTersedia($gudangId) + $stokTelur->details->sum('jumlah_butir');
        if ($butirTotal > $stokTersedia) {
            return back()->with('error', "Stok tidak mencukupi. Stok tersedia: " . number_format($stokTersedia - $stokTelur->details->sum('jumlah_butir')) . " butir.")->withInput();
        }

        $stokTelur->update([
            'tanggal' => $request->tanggal,
            'driver' => $request->driver,
            'jumlah_butir' => $butirTotal,
            'berat_kg' => $petis->sum('berat'),
            'karpet' => $petis->sum('karpet'),
            'peti' => $petis->count(),
            'keterangan' => $request->keterangan,
        ]);

        $stokTelur->details()->delete();
        foreach ($petis as $p) {
            $stokTelur->details()->create([
                'sortasi_telur_detail_id' => $p->id,
                'jumlah_butir' => $p->butir,
                'berat_kg' => $p->berat,
                'karpet' => $p->karpet,
                'peti' => 1,
            ]);
        }

        return redirect()->route('telur.keluar.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(StokTelurKeluar $stokTelur)
    {
        if ($stokTelur->penjualanStokDetails()->exists()) {
            return back()->with('error', 'Data sudah terjual, tidak bisa dihapus.');
        }
        $stokTelur->details()->delete();
        $stokTelur->delete();
        return redirect()->route('telur.keluar.index')->with('success', 'Data berhasil dihapus.');
    }

    public function suratJalan(StokTelurKeluar $stokTelur)
    {
        $stokTelur->load(['gudang', 'user', 'details.sortasiDetail.sortasiTelur.kandang', 'ttdPengirim', 'ttdMengetahui']);
        return view('telur.keluar.surat-jalan', compact('stokTelur'));
    }

    public function downloadSuratJalan(StokTelurKeluar $stokTelur)
    {
        $stokTelur->load(['gudang', 'user', 'details.sortasiDetail.sortasiTelur.kandang', 'ttdPengirim', 'ttdMengetahui']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('telur.keluar.surat-jalan-pdf', compact('stokTelur'));
        return $pdf->download('Surat-Jalan-' . $stokTelur->no_referensi . '.pdf');
    }

    public function ttdSuratJalan(Request $request, StokTelurKeluar $stokTelur)
    {
        $user = auth()->user();
        $posisi = $request->get('posisi');
        $field = 'ttd_' . $posisi;

        if (!in_array($posisi, ['pengirim', 'mengetahui'])) abort(400);

        if ($stokTelur->$field && !$user->hasRole('super_admin')) {
            return back()->with('error', 'TTD sudah terisi.');
        }

        if ($posisi === 'pengirim' && !$user->hasAnyRole(['petugas_kandang', 'super_admin'])) abort(403);
        if ($posisi === 'mengetahui' && !$user->hasRole('super_admin')) abort(403);

        $update = [$field => $user->id, $field . '_at' => now()];
        $sig = $request->get('signature');
        if ($sig) {
            $update[$field . '_img'] = $sig;
        }

        $stokTelur->update($update);
        return back()->with('success', 'TTD berhasil.');
    }

    public function kartuStok(Request $request)
    {
        $gudangId = $request->get('gudang_id');
        $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $masukQuery = SortasiTelurDetail::with(['sortasiTelur.gudang']);
        if ($gudangId) {
            $masukQuery->whereHas('sortasiTelur', fn($q) => $q->where('gudang_id', $gudangId));
        }
        $masuk = $masukQuery->get()->map(function ($d) {
            $s = $d->sortasiTelur;
            return (object) [
                'tanggal' => $s->tanggal,
                'tipe' => 'Masuk (Sortir)',
                'kandang' => $s->gudang->nama_gudang ?? '-',
                'butir' => $d->butir,
                'kg' => $d->berat,
                'ket' => 'Shift ' . ucfirst($s->shift) . ' - Peti sortir',
            ];
        });

        $keluarQuery = StokTelurKeluar::with(['gudang', 'details.sortasiDetail.sortasiTelur.kandang']);
        if (!auth()->user()->hasRole('super_admin')) {
            $gudangIds = $this->getUserGudangIds();
            if (!empty($gudangIds)) $keluarQuery->whereIn('gudang_id', $gudangIds);
        }
        if ($gudangId) $keluarQuery->where('gudang_id', $gudangId);
        $keluar = $keluarQuery->whereBetween('tanggal', [$dari, $sampai])->orderBy('tanggal')->get()
            ->map(function ($k) {
                return (object) [
                    'tanggal' => $k->tanggal,
                    'tipe' => 'Keluar',
                    'kandang' => $k->gudang->nama_gudang ?? '-',
                    'butir' => -$k->jumlah_butir,
                    'kg' => -$k->berat_kg,
                    'ket' => ($k->driver ? 'Driver: ' . $k->driver . ' - ' : '') . ($k->keterangan ?? 'Distribusi'),
                ];
            });

        $eceranQuery = StokTelurEceran::with('gudang');
        if (!auth()->user()->hasRole('super_admin')) {
            $gudangIds = $this->getUserGudangIds();
            if (!empty($gudangIds)) $eceranQuery->whereIn('gudang_id', $gudangIds);
        }
        if ($gudangId) $eceranQuery->where('gudang_id', $gudangId);
        $eceran = $eceranQuery->whereBetween('tanggal', [$dari, $sampai])->orderBy('tanggal')->get()
            ->map(function ($e) {
                return (object) [
                    'tanggal' => $e->tanggal,
                    'tipe' => 'Eceran (Alokasi)',
                    'kandang' => $e->gudang->nama_gudang ?? '-',
                    'butir' => -$e->jumlah_butir,
                    'kg' => -$e->berat_kg,
                    'ket' => $e->no_referensi . ' - ' . ($e->keterangan ?? 'Alokasi eceran'),
                ];
            });

        $merged = $masuk->concat($keluar)->concat($eceran)->sortBy('tanggal')->values();
        $running = 0;
        foreach ($merged as $r) {
            $running += $r->butir;
            $r->saldo = $running;
        }

        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();
        return view('telur.keluar.kartu', compact('merged', 'gudangs', 'gudangId', 'dari', 'sampai'));
    }
}
