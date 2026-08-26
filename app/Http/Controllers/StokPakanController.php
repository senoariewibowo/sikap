<?php

namespace App\Http\Controllers;

use App\Models\StokPakan;
use App\Models\Kandang;
use App\Models\JenisPakan;
use App\Models\ProduksiTelur;
use App\Http\Requests\StoreStokPakanRequest;
use Illuminate\Http\Request;

class StokPakanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $kandangId = $request->get('kandang_id');
        $jenisId = $request->get('jenis_pakan_id');
        $kategori = $request->get('kategori', 'pakan');
        $tipe = $request->get('tipe');
        $tanggalStart = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $tanggalEnd = $request->get('sampai', now()->format('Y-m-d'));
        $ids = $user->hasRole('super_admin') ? [] : $this->getUserKandangIds();

        $query = StokPakan::with(['jenisPakan', 'kandang', 'user'])
            ->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if (!$user->hasRole('super_admin') && !empty($ids)) {
            $query->where(function ($q) use ($ids) { $q->whereIn('kandang_id', $ids)->orWhereNull('kandang_id'); });
        }
        if ($kandangId) $query->where('kandang_id', $kandangId);
        if ($jenisId) $query->where('jenis_pakan_id', $jenisId);
        if ($kategori && $kategori !== 'semua') {
            $katIds = $kategori === 'obat' ? ['obat', 'vitamin'] : [$kategori];
            $query->whereHas('jenisPakan', fn($q) => $q->whereIn('kategori', $katIds));
        }
        if ($tipe) $query->where('tipe', $tipe);
        $query->whereBetween('tanggal', [$tanggalStart, $tanggalEnd]);
        $stoks = $query->paginate(15)->withQueryString();

        $kandangs = $this->getUserKandangs()->get();
        $jenisPakans = JenisPakan::when($kategori && $kategori !== 'semua', function ($q) use ($kategori) {
            $katIds = $kategori === 'obat' ? ['obat', 'vitamin'] : [$kategori];
            $q->whereIn('kategori', $katIds);
        })->orderBy('nama')->get();

        $rekapStok = JenisPakan::when($kategori && $kategori !== 'semua', function ($q) use ($kategori) {
                $katIds = $kategori === 'obat' ? ['obat', 'vitamin'] : [$kategori];
                $q->whereIn('kategori', $katIds);
            })->withCount(['stokPakan as total_masuk' => fn($q) => $q->where('tipe', 'masuk')])
            ->withCount(['stokPakan as total_keluar' => fn($q) => $q->where('tipe', 'keluar')])
            ->get()->map(function ($item) use ($user, $ids) {
                $masukQ = $item->stokPakan()->where('tipe', 'masuk');
                $keluarQ = $item->stokPakan()->where('tipe', 'keluar');
                $pemakaianQ = \App\Models\PemakaianPakan::where('jenis_pakan_id', $item->id);

                if (!$user->hasRole('super_admin') && !empty($ids)) {
                    $masukQ->whereIn('kandang_id', $ids);
                    $keluarQ->whereIn('kandang_id', $ids);
                    $pemakaianQ->whereIn('kandang_id', $ids);
                }

                $item->total_masuk = $masukQ->sum('jumlah_kg');
                $item->total_keluar = $keluarQ->sum('jumlah_kg');
                $item->sisa = $item->total_masuk - $item->total_keluar - $pemakaianQ->sum('jumlah');
                return $item;
            });

        $kategoriLabel = $kategori === 'obat' ? 'Obat & Vitamin' : ucfirst($kategori);
        $stokMenipis = JenisPakan::all()->filter(fn($j) => $j->isStokMenipis());
        $fcrData = $this->hitungFCR($kandangId, $tanggalStart, $tanggalEnd);

        return view('pakan.stok.index', compact(
            'stoks', 'kandangs', 'jenisPakans', 'kandangId', 'jenisId', 'tipe',
            'tanggalStart', 'tanggalEnd', 'rekapStok', 'stokMenipis', 'fcrData', 'kategori', 'kategoriLabel'
        ));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $kategori = $request->get('kategori', 'pakan');

        if ($user->hasRole('super_admin')) {
            $kandangs = Kandang::orderBy('nama_kandang')->get();
        } elseif ($user->hasRole('petugas_kandang')) {
            $kandangs = $this->getUserKandangs()->get();
        } else {
            abort(403);
        }

        $jenisPakans = JenisPakan::when($kategori !== 'semua', fn($q) => $q->whereIn('kategori', $kategori === 'obat' ? ['obat','vitamin'] : [$kategori]))->orderBy('nama')->get();
        $isPetugas = $user->hasRole('petugas_kandang');

        return view('pakan.stok.create', compact('kandangs', 'jenisPakans', 'kategori', 'isPetugas'));
    }

    public function store(StoreStokPakanRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        if ($user->hasRole('petugas_kandang')) {
            $data['tipe'] = 'keluar';
            $data['kandang_id'] = $this->getUserKandangIds()[0] ?? null;
        }

        if ($data['tipe'] === 'keluar') {
            $jenis = JenisPakan::find($data['jenis_pakan_id']);
            if ($data['kandang_id']) {
                $stokKandang = StokPakan::where('jenis_pakan_id', $data['jenis_pakan_id'])
                    ->where('kandang_id', $data['kandang_id'])->where('tipe', 'masuk')->sum('jumlah_kg')
                    - StokPakan::where('jenis_pakan_id', $data['jenis_pakan_id'])
                    ->where('kandang_id', $data['kandang_id'])->where('tipe', 'keluar')->sum('jumlah_kg')
                    - \App\Models\PemakaianPakan::where('jenis_pakan_id', $data['jenis_pakan_id'])
                    ->where('kandang_id', $data['kandang_id'])->sum('jumlah');
            } else {
                $stokKandang = $jenis->stokSekarang();
            }
            if ($data['jumlah_kg'] > $stokKandang) {
                $lokasi = $data['kandang_id'] ? Kandang::find($data['kandang_id'])->nama_kandang : 'Gudang Pusat';
                return back()->with('error', "Stok tidak mencukupi. Stok {$jenis->nama} di {$lokasi}: " . number_format($stokKandang, 0, ',', '.') . " {$jenis->satuan}.")->withInput();
            }
        }

        $data['created_by'] = $user->id;
        StokPakan::create($data);
        return redirect()->route('pakan.stok.index', ['kategori' => $request->get('kategori', 'pakan')])->with('success', 'Transaksi stok berhasil dicatat.');
    }

    public function edit(StokPakan $stokPakan)
    {
        $user = auth()->user();
        if ($user->hasRole('petugas_kandang') && $stokPakan->created_by !== $user->id) {
            abort(403);
        }

        $isPetugas = $user->hasRole('petugas_kandang');
        $kandangs = $user->hasRole('super_admin') ? Kandang::orderBy('nama_kandang')->get() : $this->getUserKandangs()->get();
        $jenisPakans = JenisPakan::orderBy('nama')->get();
        return view('pakan.stok.edit', compact('stokPakan', 'kandangs', 'jenisPakans', 'isPetugas'));
    }

    public function update(StoreStokPakanRequest $request, StokPakan $stokPakan)
    {
        $user = auth()->user();
        if ($user->hasRole('petugas_kandang') && $stokPakan->created_by !== $user->id) {
            abort(403);
        }

        $data = $request->validated();
        if ($user->hasRole('petugas_kandang')) {
            $data['tipe'] = 'keluar';
        }

        if ($data['tipe'] === 'keluar') {
            $jenis = JenisPakan::find($data['jenis_pakan_id']);
            $stokSekarang = $jenis->stokSekarang() + $stokPakan->jumlah_kg;
            $maxBoleh = $stokPakan->tipe === 'keluar' ? $stokSekarang : $jenis->stokSekarang();
            if ($data['jumlah_kg'] > $maxBoleh) {
                return back()->with('error', "Stok tidak mencukupi.")->withInput();
            }
        }

        $stokPakan->update($data);
        return redirect()->route('pakan.stok.index', ['kategori' => $request->get('kategori', 'pakan')])->with('success', 'Data stok berhasil diperbarui.');
    }

    public function destroy(StokPakan $stokPakan)
    {
        if (!auth()->user()->hasRole('super_admin')) abort(403);

        if ($stokPakan->tipe === 'masuk') {
            $jenis = $stokPakan->jenisPakan;
            $stokSetelahHapus = $jenis->stokSekarang() - $stokPakan->jumlah_kg;
            if ($stokSetelahHapus < 0) {
                return back()->with('error', "Tidak dapat menghapus: stok {$jenis->nama} akan minus.");
            }
        }
        $stokPakan->delete();
        return redirect()->route('pakan.stok.index')->with('success', 'Data stok berhasil dihapus.');
    }

    public function pemakaian(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('petugas_kandang')) abort(403);

        $ids = $this->getUserKandangIds();
        if (empty($ids)) return back()->with('error', 'Anda tidak ditugaskan ke kandang manapun.');

        $request->validate([
            'jenis_pakan_id' => 'required|exists:jenis_pakan,id',
            'jumlah' => 'required|numeric|min:0.01',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $kandangId = $ids[0];
        $jenis = JenisPakan::findOrFail($request->jenis_pakan_id);

        $stokMasuk = StokPakan::where('jenis_pakan_id', $jenis->id)
            ->where('kandang_id', $kandangId)->where('tipe', 'masuk')->sum('jumlah_kg');
        $stokKeluar = StokPakan::where('jenis_pakan_id', $jenis->id)
            ->where('kandang_id', $kandangId)->where('tipe', 'keluar')->sum('jumlah_kg');
        $pemakaianSudah = \App\Models\PemakaianPakan::where('jenis_pakan_id', $jenis->id)
            ->where('kandang_id', $kandangId)->sum('jumlah');
        $stokTersedia = $stokMasuk - $stokKeluar - $pemakaianSudah;

        if ($request->jumlah > $stokTersedia) {
            return back()->with('error', "Stok tidak mencukupi. {$jenis->nama} tersedia: " . number_format($stokTersedia, 0, ',', '.') . " {$jenis->satuan}.")->withInput();
        }

        \App\Models\PemakaianPakan::create([
            'jenis_pakan_id' => $jenis->id,
            'kandang_id' => $kandangId,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'created_by' => $user->id,
        ]);

        return redirect()->route('pakan.pemakaian.index', ['kategori' => $request->kategori ?? 'pakan'])
            ->with('success', "Penggunaan {$jenis->nama} berhasil dicatat: " . number_format($request->jumlah, 0, ',', '.') . " {$jenis->satuan}.");
    }

    public function pemakaianIndex(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('petugas_kandang')) abort(403);

        $ids = $this->getUserKandangIds();
        $kandangId = $ids[0] ?? null;
        $kategori = $request->get('kategori', 'pakan');

        $kandang = $kandangId ? Kandang::find($kandangId) : null;

        $pemakaians = \App\Models\PemakaianPakan::with(['jenisPakan', 'user'])
            ->where('kandang_id', $kandangId);
        if ($kategori && $kategori !== 'semua') {
            $katIds = $kategori === 'obat' ? ['obat', 'vitamin'] : [$kategori];
            $pemakaians->whereHas('jenisPakan', fn($q) => $q->whereIn('kategori', $katIds));
        }
        $pemakaians = $pemakaians->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $jenisPakans = JenisPakan::when($kategori !== 'semua', fn($q) => $q->whereIn('kategori', $kategori === 'obat' ? ['obat','vitamin'] : [$kategori]))->orderBy('nama')->get();

        $stokData = [];
        foreach ($jenisPakans as $jp) {
            $masuk = StokPakan::where('jenis_pakan_id', $jp->id)->where('kandang_id', $kandangId)->where('tipe', 'masuk')->sum('jumlah_kg');
            $keluar = StokPakan::where('jenis_pakan_id', $jp->id)->where('kandang_id', $kandangId)->where('tipe', 'keluar')->sum('jumlah_kg');
            $pakai = \App\Models\PemakaianPakan::where('jenis_pakan_id', $jp->id)->where('kandang_id', $kandangId)->sum('jumlah');
            $stokData[$jp->id] = $masuk - $keluar - $pakai;
        }

        $kategoriLabel = $kategori === 'obat' ? 'Obat & Vitamin' : ucfirst($kategori);

        return view('pakan.pemakaian.index', compact('pemakaians', 'jenisPakans', 'kandang', 'kategori', 'kategoriLabel', 'stokData'));
    }

    private function hitungFCR($kandangId, $dari, $sampai): array
    {
        $result = [];
        $kandangs = $this->getUserKandangs()->when($kandangId, fn($q) => $q->where('id', $kandangId))->where('status', 'aktif')->get();
        foreach ($kandangs as $kandang) {
            $totalPakan = StokPakan::where('kandang_id', $kandang->id)->where('tipe', 'keluar')
                ->whereBetween('tanggal', [$dari, $sampai])->sum('jumlah_kg');
            $pemakaian = \App\Models\PemakaianPakan::where('kandang_id', $kandang->id)
                ->whereBetween('tanggal', [$dari, $sampai])->sum('jumlah');
            $totalPakanAll = $totalPakan + $pemakaian;
            $totalTelur = \App\Models\SetoranTelur::where('kandang_id', $kandang->id)
                ->whereBetween('tanggal_setor', [$dari, $sampai])->sum('berat');
            $result[] = (object) ['kandang' => $kandang, 'total_pakan' => $totalPakanAll, 'total_telur' => $totalTelur,
                'fcr' => $totalTelur > 0 ? round($totalPakanAll / $totalTelur, 2) : 0];
        }
        return $result;
    }
}
