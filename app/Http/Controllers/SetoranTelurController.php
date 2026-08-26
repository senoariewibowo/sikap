<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\SetoranTelur;
use App\Models\Kandang;
use App\Models\SortasiTelur;
use App\Models\SortasiTelurDetail;
use App\Models\StokTelurEceran;
use App\Models\StokTelurKeluarDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetoranTelurController extends Controller
{
    public function review(Request $request)
    {
        $user = auth()->user();
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $shift = $request->get('shift', '');
        $gudangId = $request->get('gudang_id');

        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();

        $setoranQuery = SetoranTelur::with(['kandang', 'produksiTelur'])
            ->whereHas('produksiTelur', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            });

        if ($shift) {
            $setoranQuery->whereHas('produksiTelur', fn($q) => $q->where('shift', $shift));
        }

        if ($gudangId) {
            $setoranQuery->where('gudang_id', $gudangId);
        } elseif (!$user->hasRole('super_admin')) {
            $gudangIds = $this->getUserGudangIds();
            if (!empty($gudangIds)) {
                $setoranQuery->whereIn('gudang_id', $gudangIds);
            } else {
                $setoranQuery->whereRaw('1=0');
            }
        }

        $setoranFlat = $setoranQuery->orderBy('tanggal_setor', 'desc')->get();
        $setorans = $setoranFlat->groupBy(function ($s) {
            return $s->produksiTelur->shift ?? 'tanpa_shift';
        })->sortKeys();

        $sortasiMap = SortasiTelur::with('detail')
            ->whereDate('tanggal', $tanggal)
            ->when($shift, fn($q) => $q->where('shift', $shift))
            ->when($gudangId, fn($q) => $q->where('gudang_id', $gudangId))
            ->when(!$user->hasRole('super_admin') && $user->gudang_id, fn($q) => $q->where('gudang_id', $user->gudang_id))
            ->get()
            ->keyBy(function ($s) {
                return $s->kandang_id . '_' . $s->shift . '_' . $s->gudang_id;
            });

        $sisaPerShift = $setorans->map(function ($items) use ($sortasiMap) {
            $total = 0;
            foreach ($items as $s) {
                $sortKey = $s->kandang_id . '_' . ($s->produksiTelur->shift ?? '') . '_' . $s->gudang_id;
                $sort = $sortasiMap->get($sortKey);
                if ($sort) {
                    $total += $sort->sisa;
                } else {
                    $total += $s->produksiTelur->sisa ?? 0;
                }
            }
            return $total;
        });

        $sortasiSisaMap = SortasiTelur::with('detail')
            ->whereDate('tanggal', $tanggal)
            ->whereNull('kandang_id')
            ->when($shift, fn($q) => $q->where('shift', $shift))
            ->when($gudangId, fn($q) => $q->where('gudang_id', $gudangId))
            ->when(!$user->hasRole('super_admin') && $user->gudang_id, fn($q) => $q->where('gudang_id', $user->gudang_id))
            ->get()
            ->keyBy(function ($s) {
                return $s->shift . '_' . $s->gudang_id;
            });

        $sameDaySisaMap = SortasiTelur::whereDate('tanggal', $tanggal)
            ->whereNull('kandang_id')
            ->when($gudangId, fn($q) => $q->where('gudang_id', $gudangId))
            ->when(!$user->hasRole('super_admin') && $user->gudang_id, fn($q) => $q->where('gudang_id', $user->gudang_id))
            ->get()
            ->keyBy(function ($s) {
                return $s->shift . '_' . $s->gudang_id;
            });

        $prevSisaSortirMap = collect();
        $prevDate = date('Y-m-d', strtotime($tanggal . ' -1 day'));
        $prevSisaSortirMap = SortasiTelur::with('detail')
            ->whereNull('kandang_id')
            ->whereDate('tanggal', $prevDate)
            ->when($gudangId, fn($q) => $q->where('gudang_id', $gudangId))
            ->when(!$user->hasRole('super_admin') && $user->gudang_id, fn($q) => $q->where('gudang_id', $user->gudang_id))
            ->get()
            ->keyBy(fn($s) => $s->shift . '_' . $s->gudang_id);

        $usedDetailIds = $this->getUsedSortasiDetailIds();
        $usedCounts = $usedDetailIds->isEmpty()
            ? collect()
            : SortasiTelurDetail::whereIn('id', $usedDetailIds)
                ->groupBy('sortasi_telur_id')
                ->selectRaw('sortasi_telur_id, count(*) as total')
                ->pluck('total', 'sortasi_telur_id');

        $rekapSisa = (object) ['total_sisa' => 0, 'tersortir' => 0, 'peti' => 0, 'berat' => 0, 'sisa' => 0, 'pecah' => 0, 'retak' => 0, 'kopong' => 0];
        foreach ($sortasiSisaMap as $key => $s) {
            $shiftKey = explode('_', $key)[0];
            $rekapSisa->total_sisa += $sisaPerShift->get($shiftKey) ?? 0;
            $rekapSisa->tersortir += $s->detail->sum('butir');
            $rekapSisa->peti += $s->detail->count();
            $rekapSisa->berat += $s->detail->sum('berat');
            $rekapSisa->sisa += $s->sisa;
            $rekapSisa->pecah += $s->pecah;
            $rekapSisa->retak += $s->retak;
            $rekapSisa->kopong += $s->kopong;
        }

        $perKandang = $setoranFlat->groupBy('kandang_id')->map(function ($items, $kandangId) use ($sortasiMap, $sortasiSisaMap) {
            $totalButir = $items->sum('butir');
            $totalKarpet = $items->sum('karpet');
            $totalSisa = $items->sum(fn($s) => $s->produksiTelur->sisa ?? 0);
            $totalTersortir = 0; $totalPeti = 0; $totalPecah = 0; $totalBerat = 0;
            $totalRetak = 0; $totalKopong = 0; $totalSisaSortasi = 0;
            foreach ($items as $s) {
                $shiftName = $s->produksiTelur->shift ?? '';
                $sortKey = $kandangId . '_' . $shiftName . '_' . $s->gudang_id;
                $sort = $sortasiMap->get($sortKey);
                if ($sort) {
                    $totalTersortir += $sort->detail->sum('butir');
                    $totalPeti += $sort->detail->count();
                    $totalBerat += $sort->detail->sum('berat');
                    $totalPecah += $sort->pecah;
                    $totalRetak += $sort->retak;
                    $totalKopong += $sort->kopong;
                    $totalSisaSortasi += $sort->sisa;
                }
            }
            return (object) [
                'kandang' => $items->first()->kandang,
                'butir' => $totalButir,
                'karpet' => $totalKarpet,
                'sisa' => $totalSisa,
                'tersortir' => $totalTersortir,
                'peti' => $totalPeti,
                'berat' => $totalBerat,
                'pecah' => $totalPecah,
                'retak' => $totalRetak,
                'kopong' => $totalKopong,
                'sisa_sortasi' => $totalSisaSortasi,
            ];
        })->values();

        return view('setoran.review', compact('setorans', 'gudangs', 'gudangId', 'tanggal', 'shift', 'sortasiMap', 'sisaPerShift', 'sortasiSisaMap', 'sameDaySisaMap', 'prevSisaSortirMap', 'perKandang', 'rekapSisa', 'usedCounts'));
    }

    public function sortasi(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['super_admin', 'petugas_gudang'])) abort(403);

        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $shift = $request->get('shift', 'siang');
        $gudangId = $request->get('gudang_id') ?: ($user->gudang_id ?? null);
        $kandangId = $request->get('kandang_id');
        $isSisaProduksi = !$kandangId;

        if (!$gudangId) {
            return back()->with('error', 'Pilih gudang terlebih dahulu.');
        }

        $gudang = \App\Models\Gudang::findOrFail($gudangId);
        $kandang = $isSisaProduksi ? null : Kandang::findOrFail($kandangId);
        $sisaProduksi = 0;

        if ($isSisaProduksi) {
            $dariKandang = SortasiTelur::where('gudang_id', $gudangId)
                ->whereNotNull('kandang_id')
                ->whereDate('tanggal', $tanggal)
                ->where('shift', $shift)
                ->sum('sisa');

            $carryover = 0;
            if ($shift === 'sore') {
                $carryover = SortasiTelur::where('gudang_id', $gudangId)
                    ->whereNull('kandang_id')
                    ->whereDate('tanggal', $tanggal)
                    ->where('shift', 'siang')
                    ->sum('sisa');
            } elseif ($shift === 'siang') {
                $prevDate = date('Y-m-d', strtotime($tanggal . ' -1 day'));
                $carryover = SortasiTelur::where('gudang_id', $gudangId)
                    ->whereNull('kandang_id')
                    ->whereDate('tanggal', $prevDate)
                    ->where('shift', 'sore')
                    ->sum('sisa');
            }

            $totalButirMasuk = $dariKandang + $carryover;
            $sisaSebelumnya = $carryover;
        } else {
            $totalButirMasuk = SetoranTelur::whereHas('produksiTelur', function ($q) use ($tanggal, $shift) {
                $q->whereDate('tanggal', $tanggal)->where('shift', $shift);
            })->where('gudang_id', $gudangId)
              ->where('kandang_id', $kandangId)
              ->sum('butir');
        }

        if ($totalButirMasuk == 0) {
            return back()->with('error', $isSisaProduksi ? 'Tidak ada sisa sortasi untuk shift ini.' : 'Tidak ada setoran untuk kandang dan shift ini.');
        }

        if ($isSisaProduksi) {
            $sortasi = SortasiTelur::with('detail')
                ->where('gudang_id', $gudangId)
                ->whereNull('kandang_id')
                ->whereDate('tanggal', $tanggal)
                ->where('shift', $shift)
                ->first();
        } else {
            $sisaSebelumnya = 0;

            $sortasi = SortasiTelur::with('detail')
                ->where('gudang_id', $gudangId)
                ->where('kandang_id', $kandangId)
                ->whereDate('tanggal', $tanggal)
                ->where('shift', $shift)
                ->first();
        }

        $usedDetailIds = $this->getUsedSortasiDetailIds();
        if ($sortasi && $this->isSortasiUsed($sortasi, $usedDetailIds)) {
            return back()->with('error', 'Sortasi tidak bisa diedit karena sudah dikeluarkan.');
        }

        $existingSisaSortir = false;
        if (!$isSisaProduksi && $sortasi) {
            $existingSisaSortir = SortasiTelur::whereNull('kandang_id')
                ->where('gudang_id', $gudangId)
                ->whereDate('tanggal', $tanggal)
                ->where('shift', $shift)
                ->exists();
        }

        $gudangs = $user->hasRole('super_admin')
            ? $this->getUserGudangs()->where('status', 'aktif')->get()
            : collect([$gudang]);

        $produksi = null;
        if (!$isSisaProduksi) {
            $produksi = \App\Models\ProduksiTelur::with('fotos')
                ->where('kandang_id', $kandangId)
                ->whereDate('tanggal', $tanggal)
                ->where('shift', $shift)
                ->first();
        }

        return view('setoran.sortasi', compact(
            'tanggal', 'shift', 'gudangId', 'kandangId', 'kandang', 'gudang', 'gudangs',
            'totalButirMasuk', 'sisaSebelumnya', 'sisaProduksi', 'sortasi', 'isSisaProduksi',
            'existingSisaSortir', 'produksi'
        ));
    }

    public function simpanSortasi(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['super_admin', 'petugas_gudang'])) abort(403);

        $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required|string',
            'gudang_id' => 'required|exists:gudang,id',
            'kandang_id' => 'nullable|exists:kandang,id',
            'total_masuk' => 'required|integer|min:0',
            'pecah' => 'nullable|integer|min:0',
            'retak' => 'nullable|integer|min:0',
            'kopong' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string|max:500',
            'detail' => 'required|array|min:1',
            'detail.*.butir' => 'required|integer|min:1',
            'detail.*.karpet' => 'nullable|integer|min:0',
            'detail.*.berat' => 'nullable|numeric|min:0',
            'detail.*.kode_peti' => 'nullable|string|max:20',
        ]);

        $pecah = (int) ($request->pecah ?? 0);
        $retak = (int) ($request->retak ?? 0);
        $kopong = (int) ($request->kopong ?? 0);
        $catatan = trim($request->catatan ?? '');
        $totalMasuk = (int) $request->total_masuk;

        $incomingDetails = collect($request->detail)
            ->map(fn($d) => [
                'kode_peti' => $d['kode_peti'] ?? '',
                'butir' => (int) ($d['butir'] ?? 0),
                'karpet' => (int) ($d['karpet'] ?? 0),
                'berat' => round((float) ($d['berat'] ?? 15), 2),
            ])
            ->sortBy('kode_peti')
            ->values();

        $existing = SortasiTelur::with('detail')
            ->where('gudang_id', $request->gudang_id)
            ->where('kandang_id', $request->kandang_id ?: null)
            ->whereDate('tanggal', $request->tanggal)
            ->where('shift', $request->shift)
            ->first();

        $usedDetailIds = $this->getUsedSortasiDetailIds();
        if ($existing && $this->isSortasiUsed($existing, $usedDetailIds)) {
            return redirect()->route('setoran.review', [
                'tanggal' => $request->tanggal,
                'shift' => $request->shift,
                'gudang_id' => $request->gudang_id,
            ])->with('error', 'Sortasi tidak bisa diedit karena sudah dikeluarkan.');
        }

        if ($existing) {
            $sisa = max(0, $totalMasuk - $pecah - $retak - $kopong - $incomingDetails->sum('butir'));
            $existingDetails = $existing->detail
                ->map(fn($d) => [
                    'kode_peti' => $d->kode_peti ?? '',
                    'butir' => (int) $d->butir,
                    'karpet' => (int) $d->karpet,
                    'berat' => round((float) $d->berat, 2),
                ])
                ->sortBy('kode_peti')
                ->values();

            if (
                (int) $existing->pecah === $pecah &&
                (int) $existing->retak === $retak &&
                (int) $existing->kopong === $kopong &&
                trim($existing->catatan ?? '') === $catatan &&
                (int) $existing->sisa === $sisa &&
                $existingDetails->count() === $incomingDetails->count() &&
                $existingDetails->toArray() === $incomingDetails->toArray()
            ) {
                return redirect()->route('setoran.review', [
                    'tanggal' => $request->tanggal,
                    'shift' => $request->shift,
                    'gudang_id' => $request->gudang_id,
                ])->with('success', 'Tidak ada perubahan data sortasi.');
            }
        }

        DB::beginTransaction();

        try {
            $sortasi = SortasiTelur::updateOrCreate(
                [
                    'gudang_id' => $request->gudang_id,
                    'kandang_id' => $request->kandang_id ?: null,
                    'tanggal' => $request->tanggal,
                    'shift' => $request->shift,
                ],
                [
                    'pecah' => $pecah,
                    'retak' => $retak,
                    'kopong' => $kopong,
                    'sisa' => 0,
                    'catatan' => $request->catatan,
                    'input_by' => $user->id,
                ]
            );

            $sortasi->detail()->delete();

            $totalTersortir = 0;
            $seq = 1;
            $prefix = $sortasi->kandang && $sortasi->kandang->initial
                ? $sortasi->kandang->initial
                : 'SS';
            $day = \Carbon\Carbon::parse($request->tanggal)->format('d');

            foreach ($request->detail as $d) {
                $kodePeti = !empty($d['kode_peti'])
                    ? $d['kode_peti']
                    : sprintf('%s-%s-%02d', $prefix, $day, $seq);
                $sortasi->detail()->create([
                    'butir' => (int)($d['butir'] ?? 0),
                    'karpet' => (int)($d['karpet'] ?? 0),
                    'berat' => (float)($d['berat'] ?? 15),
                    'kode_peti' => $kodePeti,
                ]);
                $totalTersortir += (int)($d['butir'] ?? 0);
                $seq++;
            }

            $telurBagus = $totalMasuk - $pecah - $retak - $kopong;
            $sisa = max(0, $telurBagus - $totalTersortir);
            $sortasi->update(['sisa' => $sisa]);

            DB::commit();

            return redirect()->route('setoran.review', [
                'tanggal' => $request->tanggal,
                'shift' => $request->shift,
                'gudang_id' => $request->gudang_id,
            ])->with('success', 'Sortasi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan sortasi: ' . $e->getMessage());
        }
    }

    public function detail(Request $request)
    {
        $user = auth()->user();
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $shift = $request->get('shift', 'siang');
        $gudangId = $request->get('gudang_id') ?: ($user->gudang_id ?? null);
        $kandangId = $request->get('kandang_id');
        $isSisaProduksi = !$kandangId;

        $gudang = $gudangId ? \App\Models\Gudang::find($gudangId) : null;
        $kandang = $kandangId ? Kandang::find($kandangId) : null;

        $sortasiQuery = SortasiTelur::with(['detail', 'gudang', 'kandang'])
            ->where('gudang_id', $gudangId)
            ->whereDate('tanggal', $tanggal)
            ->where('shift', $shift);

        if ($isSisaProduksi) {
            $sortasiQuery->whereNull('kandang_id');
        } else {
            $sortasiQuery->where('kandang_id', $kandangId);
        }

        $sortasi = $sortasiQuery->first();

        if (!$sortasi) {
            return redirect()->route('setoran.review', ['tanggal' => $tanggal, 'shift' => $shift, 'gudang_id' => $gudangId])
                ->with('error', 'Sortasi belum diinput untuk kandang dan shift ini.');
        }

        $tersortir = $sortasi->detail->sum('butir');
        $telurBagus = $sortasi->butirMasuk() - $sortasi->pecah - $sortasi->retak - $sortasi->kopong;
        $sisa = max(0, $telurBagus - $tersortir);

        return view('setoran.detail', compact('sortasi', 'tersortir', 'telurBagus', 'sisa', 'tanggal', 'shift', 'gudang', 'gudangId', 'kandang', 'kandangId', 'isSisaProduksi'));
    }

    public function gudangStok(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('petugas_gudang')) {
            if (!$user->gudang_id) {
                return view('setoran.gudang', ['perGudang' => collect(), 'gudangs' => collect()]);
            }
            return redirect()->route('setoran.gudang.detail', $user->gudang_id);
        }

        $gudangId = $request->get('gudang_id');
        $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();

        $sortasiDetailAll = SortasiTelurDetail::with(['sortasiTelur.gudang'])->get();

        $perGudang = $gudangs->filter(function ($g) use ($sortasiDetailAll) {
            return $sortasiDetailAll->where('sortasiTelur.gudang_id', $g->id)->sum('butir') > 0
                || \App\Models\StokTelurKeluar::where('gudang_id', $g->id)->sum('jumlah_butir') > 0
                || StokTelurEceran::where('gudang_id', $g->id)->sum('jumlah_butir') > 0;
        })->map(function ($g) use ($sortasiDetailAll) {
            $masuk = $sortasiDetailAll->where('sortasiTelur.gudang_id', $g->id)->sum('butir');
            $cust = \App\Models\StokTelurKeluar::where('gudang_id', $g->id)->sum('jumlah_butir');
            $ecr = StokTelurEceran::where('gudang_id', $g->id)->sum('jumlah_butir');
            return (object) [
                'gudang' => $g,
                'masuk' => $masuk,
                'keluar_customer' => $cust,
                'keluar_eceran' => $ecr,
                'sisa' => $masuk - $cust - $ecr,
                'peti' => $sortasiDetailAll->where('sortasiTelur.gudang_id', $g->id)->count(),
            ];
        })->values();

        return view('setoran.gudang', compact('perGudang', 'gudangs', 'gudangId'));
    }

    private function getUsedSortasiDetailIds(): \Illuminate\Support\Collection
    {
        $keluar = StokTelurKeluarDetail::pluck('sortasi_telur_detail_id');
        $eceran = StokTelurEceran::whereNotNull('sortasi_telur_detail_id')->pluck('sortasi_telur_detail_id');
        return $keluar->merge($eceran)->unique()->filter();
    }

    private function isSortasiUsed(SortasiTelur $sortasi, \Illuminate\Support\Collection $usedDetailIds): bool
    {
        if ($usedDetailIds->isEmpty()) {
            return false;
        }
        return $sortasi->detail->pluck('id')->intersect($usedDetailIds)->isNotEmpty();
    }

    private function buildGudangKpi(Gudang $gudang): array
    {
        $gid = $gudang->id;
        $setorans = SetoranTelur::with(['kandang', 'produksiTelur'])->where('gudang_id', $gid)->orderBy('tanggal_setor', 'desc')->get();
        $sortasiRecords = SortasiTelur::with(['detail', 'kandang'])->where('gudang_id', $gid)->orderBy('tanggal', 'desc')->orderBy('shift')->get();
        $kandangSortasi = $sortasiRecords->whereNotNull('kandang_id');
        $sisaBatchOnly = $sortasiRecords->whereNull('kandang_id');
        $sisaBatch = $sortasiRecords->whereNull('kandang_id');
        $lastSisa = $sisaBatch->sortByDesc(fn($s) => $s->tanggal->format('Y-m-d') . '_' . $s->shift)->first();

        $sortasiMap = $sortasiRecords->keyBy(function ($s) {
            return ($s->kandang_id ?? 'sisa') . '_' . $s->tanggal->format('Y-m-d') . '_' . $s->shift . '_' . $s->gudang_id;
        });
        $grouped = $setorans->groupBy(fn($s) => $s->produksiTelur->shift ?? 'tanpa_shift')->sortKeys();

        $kpis = [
            'setoran_karpet'    => $setorans->sum('karpet'),
            'setoran_butir'     => $setorans->sum('butir'),
            'sortir_tersortir'  => $kandangSortasi->flatMap->detail->sum('butir'),
            'sortir_peti'       => $kandangSortasi->flatMap->detail->count(),
            'sortir_berat'      => $kandangSortasi->flatMap->detail->sum('berat'),
            'sortir_sisa'       => $kandangSortasi->sum('sisa'),
            'batch_tersortir'   => $sisaBatchOnly->flatMap->detail->sum('butir'),
            'batch_peti'        => $sisaBatchOnly->flatMap->detail->count(),
            'batch_sisa'        => $lastSisa ? $lastSisa->sisa : 0,
            'batch_pecah'       => $sisaBatchOnly->sum('pecah'),
            'batch_retak'       => $sisaBatchOnly->sum('retak'),
            'batch_kopong'      => $sisaBatchOnly->sum('kopong'),
            'final_tersortir'   => $kandangSortasi->flatMap->detail->sum('butir') + $sisaBatchOnly->flatMap->detail->sum('butir'),
            'final_peti'        => $kandangSortasi->flatMap->detail->count() + $sisaBatchOnly->flatMap->detail->count(),
            'final_berat'       => $kandangSortasi->flatMap->detail->sum('berat') + $sisaBatchOnly->flatMap->detail->sum('berat'),
        ];

        $custKeluar = \App\Models\StokTelurKeluar::where('gudang_id', $gid)->sum('jumlah_butir');
        $custPeti = \App\Models\StokTelurKeluar::where('gudang_id', $gid)->sum('peti');
        $custBerat = (float) \App\Models\StokTelurKeluar::where('gudang_id', $gid)->sum('berat_kg');
        $eceranAlloc = StokTelurEceran::where('gudang_id', $gid)->sum('jumlah_butir');
        $eceranPeti = StokTelurEceran::where('gudang_id', $gid)->whereNotNull('sortasi_telur_detail_id')->count();
        $eceranBerat = (float) StokTelurEceran::where('gudang_id', $gid)->sum('berat_kg');

        $stokStats = [
            'customer_keluar' => $custKeluar,
            'customer_keluar_peti' => $custPeti,
            'customer_keluar_berat' => $custBerat,
            'eceran_allocated' => $eceranAlloc,
            'eceran_peti' => $eceranPeti,
            'eceran_berat' => $eceranBerat,
        ];

        $eceranAllocations = StokTelurEceran::with(['gudang', 'user', 'transaksis'])
            ->where('gudang_id', $gid)
            ->orderBy('tanggal', 'desc')->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'eceran_page')->withQueryString();

        return compact('kpis', 'grouped', 'sortasiMap', 'stokStats', 'eceranAllocations');
    }

    public function gudangDetail(Gudang $gudang)
    {
        $user = auth()->user();
        if ($user->hasRole('petugas_gudang') && $user->gudang_id && $gudang->id != $user->gudang_id) {
            abort(403);
        }

        $data = $this->buildGudangKpi($gudang);
        return view('setoran.gudang-detail', array_merge(['gudang' => $gudang], $data));
    }
}
