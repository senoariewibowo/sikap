<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\HargaTelurController;
use App\Http\Controllers\JenisPakanController;
use App\Http\Controllers\KandangController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\ObatPemakaianController;
use App\Http\Controllers\ObatStokController;
use App\Http\Controllers\PakanController;
use App\Http\Controllers\PakanDistribusiController;
use App\Http\Controllers\PakanStokController;
use App\Http\Controllers\PemakaianPakanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PopulasiAyamController;
use App\Http\Controllers\ProduksiTelurController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SetoranTelurController;
use App\Http\Controllers\StokPakanController;
use App\Http\Controllers\TelurKeluarController;
use App\Http\Controllers\StokTelurEceranController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TransaksiEceranController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'))->middleware('auth');

Route::middleware(['auth', 'verified', 'role:super_admin,petugas_kandang,petugas_gudang,viewer,driver'])->group(function () {
    Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $kandangIds = $user->kandangIds();
        $kandangQuery = !empty($kandangIds) && !$user->hasRole('super_admin')
            ? \App\Models\Kandang::whereIn('id', $kandangIds)
            : \App\Models\Kandang::query();

        $totalKandang = (clone $kandangQuery)->where('status', 'aktif')->count();
        $totalKaryawan = \App\Models\Karyawan::where('status', 'aktif')->count();
        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));
        $scopeQuery = fn($q) => !$user->hasRole('super_admin') ? $q->whereIn('kandang_id', $kandangIds) : $q;
        $gudangIds = auth()->user()->hasRole('super_admin') ? [] : ($user->gudang_id ? [$user->gudang_id] : []);
        $scopeQueryGudang = fn($q) => !$user->hasRole('super_admin') && !empty($gudangIds) ? $q->whereIn('gudang_id', $gudangIds) : $q;

        $totalPopulasi = 0;
        $kandangList = (clone $kandangQuery)->where('status', 'aktif')->get();
        foreach ($kandangList as $k) { $totalPopulasi += $k->populasiSekarang(); }

        $produksi = (int) ($scopeQuery(\App\Models\SetoranTelur::whereBetween('tanggal_setor', [$dari, $sampai]))->sum('butir') ?? 0);
        $tersortir = (int) (\App\Models\SortasiTelurDetail::whereHas('sortasiTelur', fn($q) => $q->whereBetween('tanggal', [$dari, $sampai]))->sum('butir') ?? 0);
        $telurKeluar = (int) ($scopeQueryGudang(\App\Models\StokTelurKeluar::whereBetween('tanggal', [$dari, $sampai]))->sum('jumlah_butir') ?? 0);
        $mati = (int) ($scopeQuery(\App\Models\PopulasiAyam::whereBetween('tanggal', [$dari, $sampai]))->sum('jumlah_mati') ?? 0);
        $afkir = (int) ($scopeQuery(\App\Models\PopulasiAyam::whereBetween('tanggal', [$dari, $sampai]))->sum('jumlah_afkir') ?? 0);
        $hdpRata = $totalPopulasi > 0 ? round($produksi / $totalPopulasi * 100, 1) : 0;

        $pecah = 0; $retak = 0; $kopong = 0; $sisaProd = 0;
        $karpetTotal = 0; $petiTotal = 0; $beratTotal = 0;
        $kapasitasTotal = (clone $kandangQuery)->where('status', 'aktif')->sum('kapasitas');
        $masukTotal = (int) ($scopeQuery(\App\Models\PopulasiAyam::whereBetween('tanggal', [$dari, $sampai]))->sum('jumlah_masuk') ?? 0);
        $utilisasi = $kapasitasTotal > 0 ? round($totalPopulasi / $kapasitasTotal * 100, 1) : 0;
        $mortalitasRate = $totalPopulasi > 0 ? round(($mati + $afkir) / $totalPopulasi * 100, 2) : 0;
        $omzet = (float) (\App\Models\TransaksiPenjualan::whereBetween('tanggal', [$dari, $sampai])->sum('total_harga') ?? 0);
        $omzetTotal = (float) (\App\Models\TransaksiPenjualan::sum('total_harga') ?? 0);
        $butirTerjual = (int) (\App\Models\TransaksiPenjualan::whereBetween('tanggal', [$dari, $sampai])->sum('jumlah_butir') ?? 0);
        $piutang = \App\Models\TransaksiPenjualan::where('status_pembayaran', 'belum_lunas')
            ->selectRaw('SUM(total_harga - dp) as sisa')->value('sisa') ?? 0;

        $pengeluaran = (float) (\App\Models\Pengeluaran::whereBetween('tanggal', [$dari, $sampai])->sum('jumlah') ?? 0);

        $stokMenipis = \App\Models\JenisPakan::all()->filter(fn($j) => $j->isStokMenipis());

        $totalGudang = \App\Models\Gudang::where('status', 'aktif')->count();
        $totalSetoran = 0;
        $pendingSetoran = 0;
        $setoranSelisihList = collect();
        $gudangStokData = collect();
        $gudangPakanData = collect();
        $stokTelurGudang = 0;
        $recentSetoran = collect();
        $gudangStokSummary = collect();

        if ($user->hasRole('super_admin')) {
            $gudangStokSummary = \App\Models\Gudang::where('status', 'aktif')
                ->get()
                ->map(function ($g) {
                    $masuk = \App\Models\SetoranTelur::where('gudang_id', $g->id)->sum('butir');
                    $totalBerat = \App\Models\SetoranTelur::where('gudang_id', $g->id)->sum('berat');
                    $totalKarpet = \App\Models\SetoranTelur::where('gudang_id', $g->id)->sum('karpet');
                    $totalPeti = \App\Models\SetoranTelur::where('gudang_id', $g->id)->sum('peti');
                    return (object) [
                        'gudang' => $g,
                        'stok_butir' => $masuk,
                        'stok_berat' => $totalBerat,
                        'stok_karpet' => $totalKarpet,
                        'stok_peti' => $totalPeti,
                    ];
                })
                ->filter(fn($s) => $s->stok_butir > 0)
                ->values();
        }

        if ($user->hasRole('petugas_gudang') && $user->gudang_id) {
            $gudangId = $user->gudang_id;
            $totalSetoran = \App\Models\SetoranTelur::where('gudang_id', $gudangId)
                ->whereBetween('tanggal_setor', [$dari, $sampai])
                ->sum('butir');
            $pendingSetoran = \App\Models\ProduksiTelur::where('status_setor', 'belum_disetor')->count();
            $telurGudangMasuk = \App\Models\SetoranTelur::where('gudang_id', $gudangId)->sum('butir');
            $telurGudangKeluar = \App\Models\PenjualanStok::whereHas('stokTelurKeluar', fn($q) => true)->sum('jumlah_butir');
            $stokTelurGudang = max(0, $telurGudangMasuk - $telurGudangKeluar);

            $recentSetoran = \App\Models\SetoranTelur::with(['kandang', 'produksiTelur', 'user'])
                ->where('gudang_id', $gudangId)
                ->orderBy('tanggal_setor', 'desc')
                ->orderBy('id', 'desc')
                ->limit(5)->get();

            $gudangPakanData = \App\Models\JenisPakan::all()->map(function ($jp) {
                $masuk = \App\Models\StokPakan::where('jenis_pakan_id', $jp->id)->where('tipe', 'masuk')->sum('jumlah_kg');
                $keluar = \App\Models\StokPakan::where('jenis_pakan_id', $jp->id)->where('tipe', 'keluar')->sum('jumlah_kg');
                $pakai = \App\Models\PemakaianPakan::where('jenis_pakan_id', $jp->id)->sum('jumlah');
                $stok = max(0, $masuk - $keluar - $pakai);
                return (object) ['jenis' => $jp, 'stok' => $stok, 'menipis' => $jp->isStokMenipis()];
            });
        }

        if ($user->hasRole('super_admin')) {
            $setoranSelisihList = \App\Models\SetoranTelur::with(['kandang', 'gudang', 'produksiTelur'])
                ->whereBetween('tanggal_setor', [$dari, $sampai])
                ->get()
                ->filter(function ($s) {
                    $prod = $s->produksiTelur->jumlah_butir ?? 0;
                    return $prod > 0 && (abs($s->selisih) / $prod * 100) > 2;
                })
                ->values();
        }

        $days = max(1, \Carbon\Carbon::parse($dari)->diffInDays(\Carbon\Carbon::parse($sampai)) + 1);

        $snapshot = $kandangList->map(function ($k) use ($dari, $sampai, $days) {
            $pop = $k->populasiSekarang();
            $prod = \App\Models\SetoranTelur::where('kandang_id', $k->id)->whereBetween('tanggal_setor', [$dari, $sampai])->sum('butir') ?? 0;
            $pecah = 0; $retak = 0; $kopong = 0; $sisa = 0;
            $keluar = 0;
            $berat = 0; $karpet = 0; $peti = 0;
            $mati = \App\Models\PopulasiAyam::where('kandang_id', $k->id)->whereBetween('tanggal', [$dari, $sampai])->sum('jumlah_mati') ?? 0;
            $afkir = \App\Models\PopulasiAyam::where('kandang_id', $k->id)->whereBetween('tanggal', [$dari, $sampai])->sum('jumlah_afkir') ?? 0;
            $masuk = \App\Models\PopulasiAyam::where('kandang_id', $k->id)->whereBetween('tanggal', [$dari, $sampai])->sum('jumlah_masuk') ?? 0;
            $hdpVal = ($pop > 0 && $days > 0) ? round(($prod / $pop) / $days * 100, 1) : 0;
            return (object) ['kandang'=>$k,'populasi'=>$pop,'produksi'=>$prod,'berat'=>$berat,'karpet'=>$karpet,'peti'=>$peti,'pecah'=>$pecah,'retak'=>$retak,'kopong'=>$kopong,'sisa'=>$sisa,'hdp'=>$hdpVal,'keluar'=>$keluar,'mati'=>$mati,'afkir'=>$afkir,'masuk'=>$masuk,'kapasitas'=>$k->kapasitas,'utilisasi'=>round($pop / max(1, $k->kapasitas) * 100, 1)];
        });

        $chartLabels = []; $chartProduksi = []; $chartMati = []; $chartAfkir = [];
        $cursor = \Carbon\Carbon::parse($dari);
        $end = \Carbon\Carbon::parse($sampai);
        while ($cursor->lte($end)) {
            $d = $cursor->format('Y-m-d');
            $chartLabels[] = $cursor->format('d/m');
            $chartProduksi[] = (int) $scopeQuery(\App\Models\ProduksiTelur::whereDate('tanggal', $d))->sum('jumlah_butir');
            $chartMati[] = (int) $scopeQuery(\App\Models\PopulasiAyam::whereDate('tanggal', $d))->sum('jumlah_mati');
            $chartAfkir[] = (int) $scopeQuery(\App\Models\PopulasiAyam::whereDate('tanggal', $d))->sum('jumlah_afkir');
            $cursor->addDay();
        }

        $chartPemakaianLabels = []; $chartPemakaianDatasets = []; $pemakaianTotal = 0;
        if ($user->hasRole('petugas_kandang') && !empty($kandangIds)) {
            $pemakaianTotal = \App\Models\PemakaianPakan::whereIn('kandang_id', $kandangIds)
                ->whereBetween('tanggal', [$dari, $sampai])->sum('jumlah');
            $pemakaianData = \App\Models\PemakaianPakan::with('jenisPakan')
                ->whereIn('kandang_id', $kandangIds)
                ->whereBetween('tanggal', [$dari, $sampai])
                ->get()
                ->groupBy('jenis_pakan_id');

            $chartPemakaianLabels = $chartLabels;
            $colors = ['#6366f1','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4','#84cc16'];
            $ci = 0;
            foreach ($pemakaianData as $jenisId => $items) {
                $jenis = $items->first()->jenisPakan;
                $perDay = array_fill(0, count($chartLabels), 0);
                foreach ($items as $it) {
                    $idx = array_search(\Carbon\Carbon::parse($it->tanggal)->format('d/m'), $chartLabels);
                    if ($idx !== false) $perDay[$idx] += $it->jumlah;
                }
                $chartPemakaianDatasets[] = [
                    'label' => $jenis->nama,
                    'data' => $perDay,
                    'backgroundColor' => $colors[$ci % count($colors)],
                ];
                $ci++;
            }
        }

        if ($user->hasRole('driver')) {
            $sjList = \App\Models\StokTelurKeluar::with('gudang')
                ->whereBetween('tanggal', [$dari, $sampai])
                ->orderBy('tanggal', 'desc')->orderBy('id', 'desc')
                ->limit(10)->get();

            $pengeluaranList = \App\Models\Pengeluaran::with('kategori')
                ->whereBetween('tanggal', [$dari, $sampai])
                ->orderBy('tanggal', 'desc')->orderBy('id', 'desc')
                ->limit(10)->get();

            $totalSJ = \App\Models\StokTelurKeluar::whereBetween('tanggal', [$dari, $sampai])->sum('jumlah_butir');
            $totalPetiSJ = \App\Models\StokTelurKeluar::whereBetween('tanggal', [$dari, $sampai])->sum('peti');
            $totalPengeluaran = \App\Models\Pengeluaran::whereBetween('tanggal', [$dari, $sampai])->sum('jumlah');

            return view('dashboard-driver', compact('dari', 'sampai', 'sjList', 'pengeluaranList', 'totalSJ', 'totalPetiSJ', 'totalPengeluaran'));
        }

        return view('dashboard', compact(
            'totalKandang', 'totalKaryawan', 'totalPopulasi',
            'produksi', 'tersortir', 'telurKeluar', 'mati', 'afkir', 'hdpRata',
            'pecah', 'retak', 'kopong', 'sisaProd',
            'karpetTotal', 'petiTotal', 'beratTotal', 'kapasitasTotal', 'masukTotal', 'utilisasi', 'mortalitasRate',
            'omzet', 'omzetTotal', 'butirTerjual', 'piutang', 'pengeluaran',
            'stokMenipis', 'snapshot',
            'chartPemakaianLabels', 'chartPemakaianDatasets', 'pemakaianTotal',
            'dari', 'sampai', 'days',
            'chartLabels', 'chartProduksi', 'chartMati', 'chartAfkir',
            'totalGudang', 'totalSetoran', 'pendingSetoran', 'setoranSelisihList',
            'gudangStokData', 'gudangPakanData', 'stokTelurGudang', 'recentSetoran',
            'gudangStokSummary'
        ));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/peta', [ReportController::class, 'peta'])->name('peta.index');
});

Route::middleware(['auth', 'verified', 'role:super_admin,petugas_kandang,petugas_gudang,viewer,driver'])->group(function () {
    Route::get('/laporan/produksi', [ReportController::class, 'produksi'])->name('laporan.produksi');
    Route::get('/laporan/mortalitas', [ReportController::class, 'mortalitas'])->name('laporan.mortalitas');
    Route::get('/laporan/pakan', [ReportController::class, 'pakan'])->name('laporan.pakan');
});

Route::middleware(['auth', 'verified', 'role:super_admin,petugas_kandang'])->group(function () {
    Route::get('/populasi', [PopulasiAyamController::class, 'index'])->name('populasi.index');
    Route::get('/populasi/create', [PopulasiAyamController::class, 'create'])->name('populasi.create');
    Route::post('/populasi', [PopulasiAyamController::class, 'store'])->name('populasi.store');
    Route::match(['get', 'post'], '/populasi/mutasi', [PopulasiAyamController::class, 'mutasi'])->name('populasi.mutasi');
    Route::get('/populasi/{populasi}', [PopulasiAyamController::class, 'show'])->name('populasi.show');
    Route::get('/populasi/{populasi}/edit', [PopulasiAyamController::class, 'edit'])->name('populasi.edit');
    Route::put('/populasi/{populasi}', [PopulasiAyamController::class, 'update'])->name('populasi.update');
    Route::delete('/populasi/{populasi}', [PopulasiAyamController::class, 'destroy'])->name('populasi.destroy');

    Route::get('/produksi', [ProduksiTelurController::class, 'index'])->name('produksi.index');
    Route::get('/produksi/create', [ProduksiTelurController::class, 'create'])->name('produksi.create');
    Route::post('/produksi', [ProduksiTelurController::class, 'store'])->name('produksi.store');
    Route::post('/produksi/{produksi}/setor', [ProduksiTelurController::class, 'setor'])->name('produksi.setor');
    Route::get('/produksi/{produksi}', [ProduksiTelurController::class, 'show'])->name('produksi.show');
    Route::get('/produksi/{produksi}/edit', [ProduksiTelurController::class, 'edit'])->name('produksi.edit');
    Route::put('/produksi/{produksi}', [ProduksiTelurController::class, 'update'])->name('produksi.update');
    Route::delete('/produksi/{produksi}', [ProduksiTelurController::class, 'destroy'])->name('produksi.destroy');
});

Route::middleware(['auth', 'verified', 'role:super_admin,petugas_kandang,petugas_gudang'])->group(function () {
    Route::get('/telur/keluar', [TelurKeluarController::class, 'index'])->name('telur.keluar.index');
    Route::get('/telur/keluar/kartu', [TelurKeluarController::class, 'kartuStok'])->name('telur.keluar.kartu');
    Route::get('/telur/keluar/{stokTelur}/surat-jalan', [TelurKeluarController::class, 'suratJalan'])->name('telur.keluar.surat-jalan');
    Route::get('/telur/keluar/{stokTelur}/surat-jalan/download', [TelurKeluarController::class, 'downloadSuratJalan'])->name('telur.keluar.surat-jalan.download');
    Route::post('/telur/keluar/{stokTelur}/ttd', [TelurKeluarController::class, 'ttdSuratJalan'])->name('telur.keluar.ttd');
    Route::get('/telur/keluar/create', [TelurKeluarController::class, 'create'])->name('telur.keluar.create');
    Route::post('/telur/keluar', [TelurKeluarController::class, 'store'])->name('telur.keluar.store');
    Route::get('/telur/keluar/{stokTelur}/edit', [TelurKeluarController::class, 'edit'])->name('telur.keluar.edit');
    Route::put('/telur/keluar/{stokTelur}', [TelurKeluarController::class, 'update'])->name('telur.keluar.update');
    Route::delete('/telur/keluar/{stokTelur}', [TelurKeluarController::class, 'destroy'])->name('telur.keluar.destroy');

    Route::get('/telur/eceran/create', [StokTelurEceranController::class, 'create'])->name('telur.eceran.create');
    Route::post('/telur/eceran', [StokTelurEceranController::class, 'store'])->name('telur.eceran.store');
    Route::get('/telur/eceran/{id}/edit', [StokTelurEceranController::class, 'edit'])->name('telur.eceran.edit');
    Route::put('/telur/eceran/{id}', [StokTelurEceranController::class, 'update'])->name('telur.eceran.update');
    Route::delete('/telur/eceran/{id}', [StokTelurEceranController::class, 'destroy'])->name('telur.eceran.destroy');

    Route::get('/laporan/produksi/excel', [ReportController::class, 'exportProduksiExcel'])->name('laporan.produksi.excel');
    Route::get('/laporan/produksi/pdf', [ReportController::class, 'exportProduksiPdf'])->name('laporan.produksi.pdf');
    Route::get('/laporan/mortalitas/excel', [ReportController::class, 'exportMortalitasExcel'])->name('laporan.mortalitas.excel');
    Route::get('/laporan/mortalitas/pdf', [ReportController::class, 'exportMortalitasPdf'])->name('laporan.mortalitas.pdf');
    Route::get('/laporan/pakan/excel', [ReportController::class, 'exportPakanExcel'])->name('laporan.pakan.excel');
    Route::get('/laporan/pakan/pdf', [ReportController::class, 'exportPakanPdf'])->name('laporan.pakan.pdf');
});

Route::middleware(['auth', 'verified', 'role:super_admin,petugas_gudang'])->group(function () {
    Route::get('/pakan/stok', [PakanStokController::class, 'index'])->name('pakan.stok.index');
    Route::get('/pakan/stok/create', [PakanStokController::class, 'create'])->name('pakan.stok.create');
    Route::post('/pakan/stok', [PakanStokController::class, 'store'])->name('pakan.stok.store');
    Route::get('/pakan/stok/{stok}/riwayat', [PakanStokController::class, 'riwayat'])->name('pakan.stok.riwayat');

    Route::get('/pakan/distribusi', [PakanDistribusiController::class, 'index'])->name('pakan.distribusi.index');
    Route::get('/pakan/distribusi/create', [PakanDistribusiController::class, 'create'])->name('pakan.distribusi.create');
    Route::post('/pakan/distribusi', [PakanDistribusiController::class, 'store'])->name('pakan.distribusi.store');
    Route::delete('/pakan/distribusi/{distribusi}', [PakanDistribusiController::class, 'destroy'])->name('pakan.distribusi.destroy');
    Route::get('/pakan/stok-ajax', [PakanDistribusiController::class, 'ajaxStok'])->name('pakan.stok-ajax');
});

Route::middleware(['auth', 'verified', 'role:super_admin,petugas_kandang'])->group(function () {
    Route::get('/pakan/pemakaian', [PemakaianPakanController::class, 'index'])->name('pakan.pemakaian.index');
    Route::post('/pakan/pemakaian', [PemakaianPakanController::class, 'store'])->name('pakan.pemakaian.store');
    Route::get('/pakan/pemakaian/{pemakaian}/edit', [PemakaianPakanController::class, 'edit'])->name('pakan.pemakaian.edit');
    Route::put('/pakan/pemakaian/{pemakaian}', [PemakaianPakanController::class, 'update'])->name('pakan.pemakaian.update');
    Route::delete('/pakan/pemakaian/{pemakaian}', [PemakaianPakanController::class, 'destroy'])->name('pakan.pemakaian.destroy');
    Route::post('/pakan/distribusi/{distribusi}/terima', [PemakaianPakanController::class, 'terima'])->name('pakan.distribusi.terima');
});

Route::middleware(['auth', 'verified', 'role:super_admin,petugas_gudang'])->group(function () {
    Route::get('/obat/stok', [ObatStokController::class, 'index'])->name('obat.stok.index');
    Route::get('/obat/stok/create', [ObatStokController::class, 'create'])->name('obat.stok.create');
    Route::post('/obat/stok', [ObatStokController::class, 'store'])->name('obat.stok.store');
    Route::get('/obat/stok/{stok}/riwayat', [ObatStokController::class, 'riwayat'])->name('obat.stok.riwayat');
});

Route::middleware(['auth', 'verified', 'role:super_admin,petugas_kandang'])->group(function () {
    Route::get('/obat/pemakaian', [ObatPemakaianController::class, 'index'])->name('obat.pemakaian.index');
    Route::post('/obat/pemakaian', [ObatPemakaianController::class, 'store'])->name('obat.pemakaian.store');
});

Route::middleware(['auth', 'verified', 'role:super_admin,petugas_kandang,petugas_gudang'])->group(function () {
    Route::redirect('/setoran', '/setoran/review')->name('setoran.index');
    Route::get('/setoran/review', [SetoranTelurController::class, 'review'])->name('setoran.review');
    Route::get('/setoran/gudang', [SetoranTelurController::class, 'gudangStok'])->name('setoran.gudang');
    Route::get('/setoran/gudang/{gudang}', [SetoranTelurController::class, 'gudangDetail'])->name('setoran.gudang.detail');
    Route::get('/setoran/review/sortasi', [SetoranTelurController::class, 'sortasi'])->name('setoran.sortasi');
    Route::put('/setoran/review/sortasi', [SetoranTelurController::class, 'simpanSortasi'])->name('setoran.simpanSortasi');
    Route::get('/setoran/review/detail', [SetoranTelurController::class, 'detail'])->name('setoran.detail');
});

Route::middleware(['auth','verified','role:super_admin,driver'])->group(function () {
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
    Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::get('/penjualan/{transaksi}/edit', [PenjualanController::class, 'edit'])->name('penjualan.edit');
    Route::put('/penjualan/{transaksi}', [PenjualanController::class, 'update'])->name('penjualan.update');
    Route::get('/penjualan/{transaksi}/invoice', [PenjualanController::class, 'invoice'])->name('penjualan.invoice');
    Route::get('/penjualan/{transaksi}/invoice/download', [PenjualanController::class, 'downloadInvoice'])->name('penjualan.invoice.download');
    Route::post('/penjualan/{transaksi}/ttd', [PenjualanController::class, 'ttdInvoice'])->name('penjualan.ttd');
    Route::get('/penjualan/{transaksi}', [PenjualanController::class, 'show'])->name('penjualan.show');
    Route::patch('/penjualan/{transaksi}/bayar', [PenjualanController::class, 'updatePembayaran'])->name('penjualan.bayar');
    Route::delete('/penjualan/{transaksi}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
    Route::get('/penjualan/export/excel', [PenjualanController::class, 'exportExcel'])->name('penjualan.excel');
    Route::get('/penjualan/export/pdf', [PenjualanController::class, 'exportPdf'])->name('penjualan.pdf');
});

Route::middleware(['auth','verified','role:driver'])->group(function () {
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{stokTelurKeluar}/surat-jalan', [TransaksiController::class, 'suratJalan'])->name('transaksi.surat-jalan');
});

Route::middleware(['auth','verified','role:super_admin'])->group(function () {
    Route::resource('customer', CustomerController::class);
});

Route::middleware(['auth','verified','role:super_admin,petugas_gudang'])->group(function () {
    Route::get('/eceran/transaksi', [TransaksiEceranController::class, 'index'])->name('eceran.transaksi.index');
    Route::get('/eceran/transaksi/create', [TransaksiEceranController::class, 'create'])->name('eceran.transaksi.create');
    Route::post('/eceran/transaksi', [TransaksiEceranController::class, 'store'])->name('eceran.transaksi.store');
    Route::get('/eceran/transaksi/{id}/edit', [TransaksiEceranController::class, 'edit'])->name('eceran.transaksi.edit');
    Route::put('/eceran/transaksi/{id}', [TransaksiEceranController::class, 'update'])->name('eceran.transaksi.update');
    Route::delete('/eceran/transaksi/{id}', [TransaksiEceranController::class, 'destroy'])->name('eceran.transaksi.destroy');
});

Route::middleware(['auth', 'verified', 'role:super_admin'])->group(function () {
    Route::resource('gudang', GudangController::class);
    Route::resource('users', UserController::class)->except(['show']);

    Route::resource('kandang', KandangController::class);
    Route::resource('karyawan', KaryawanController::class);
    Route::post('/karyawan/{karyawan}/assign-kandang', [KaryawanController::class, 'assignKandang'])->name('karyawan.assign-kandang');
    Route::delete('/karyawan/{karyawan}/unassign-kandang/{kandang}', [KaryawanController::class, 'unassignKandang'])->name('karyawan.unassign-kandang');
    Route::post('/karyawan/{karyawan}/assign-gudang', [KaryawanController::class, 'assignGudang'])->name('karyawan.assign-gudang');
    Route::delete('/karyawan/{karyawan}/unassign-gudang/{gudang}', [KaryawanController::class, 'unassignGudang'])->name('karyawan.unassign-gudang');

    Route::get('/harga', [HargaTelurController::class, 'index'])->name('harga.index');
    Route::get('/harga/create', [HargaTelurController::class, 'create'])->name('harga.create');
    Route::post('/harga', [HargaTelurController::class, 'store'])->name('harga.store');
    Route::get('/harga/{harga}/edit', [HargaTelurController::class, 'edit'])->name('harga.edit');
    Route::put('/harga/{harga}', [HargaTelurController::class, 'update'])->name('harga.update');
    Route::delete('/harga/{harga}', [HargaTelurController::class, 'destroy'])->name('harga.destroy');

    Route::resource('pakan', PakanController::class);
    Route::resource('obat', ObatController::class);

    Route::prefix('keuangan')->name('keuangan.')->group(function () {
        Route::resource('kategori', KategoriPengeluaranController::class)->except(['show']);
        Route::resource('pengeluaran', PengeluaranController::class);
        Route::get('/pengeluaran/export/excel', [PengeluaranController::class, 'exportExcel'])->name('pengeluaran.excel');
        Route::get('/pengeluaran/export/pdf', [PengeluaranController::class, 'exportPdf'])->name('pengeluaran.pdf');
    });
});

require __DIR__.'/auth.php';
