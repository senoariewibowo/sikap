<?php

namespace App\Http\Controllers;

use App\Models\ProduksiTelur;
use App\Models\Kandang;
use App\Models\SetoranTelur;
use App\Http\Requests\StoreProduksiTelurRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProduksiTelurController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $kandangId = $request->get('kandang_id');
        $tanggalStart = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $tanggalEnd = $request->get('sampai', now()->format('Y-m-d'));
        $statusSetor = $request->get('status_setor');

        $kandangs = $this->getUserKandangs()->get();
        $isResume = ($user->hasRole('super_admin') && !$kandangId);

        if ($isResume) {
            $resume = $this->scopeByUser(
                ProduksiTelur::selectRaw('kandang_id, SUM(jumlah_butir) as total_butir, SUM(karpet) as total_karpet, SUM(sisa) as total_sisa, COUNT(DISTINCT tanggal) as hari_produksi')
                ->whereBetween('tanggal', [$tanggalStart, $tanggalEnd])
                ->groupBy('kandang_id')->with('kandang')
            )->get();

            foreach ($resume as $r) {
                $pop = $r->kandang->populasiSekarang();
                $r->hdp = $pop > 0 && $r->hari_produksi > 0 ? round($r->total_butir / ($pop * $r->hari_produksi) * 100, 1) : 0;
            }

            return view('produksi.index', compact('resume', 'kandangs', 'kandangId', 'tanggalStart', 'tanggalEnd', 'isResume'));
        }

        $query = ProduksiTelur::with(['kandang', 'user', 'setoran'])
            ->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
        $query = $this->scopeByUser($query);
        if ($kandangId) $query->where('kandang_id', $kandangId);
        if ($statusSetor) $query->where('status_setor', $statusSetor);
        $query->whereBetween('tanggal', [$tanggalStart, $tanggalEnd]);
        $produksis = $query->paginate(15)->withQueryString();

        $rekapPerKandang = $this->scopeByUser(
            ProduksiTelur::selectRaw('kandang_id, SUM(jumlah_butir) as total_butir, COUNT(*) as hari_produksi')
        )->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$tanggalStart, $tanggalEnd])->groupBy('kandang_id')->with('kandang')->get();

        foreach ($rekapPerKandang as $rekap) {
            $populasiSekarang = $rekap->kandang->populasiSekarang();
            $rekap->hdp = $populasiSekarang > 0 && $rekap->hari_produksi > 0
                ? round($rekap->total_butir / ($populasiSekarang * $rekap->hari_produksi) * 100, 2) : 0;
        }

        return view('produksi.index', compact('produksis', 'kandangs', 'kandangId', 'tanggalStart', 'tanggalEnd', 'rekapPerKandang', 'isResume', 'statusSetor'));
    }

    public function create()
    {
        $kandangs = $this->getUserKandangs()->where('status', 'aktif')->get();
        return view('produksi.create', compact('kandangs'));
    }

    public function store(StoreProduksiTelurRequest $request)
    {
        $data = $request->validated();
        unset($data['foto_base64'], $data['foto']);
        $data['input_by'] = auth()->id();
        $data['status_setor'] = 'belum_disetor';
        $data['sisa'] = (int) ($data['sisa'] ?? 0);
        $data['jumlah_butir'] = ($data['karpet'] * 30) + $data['sisa'];

        $produksi = ProduksiTelur::create($data);

        $this->simpanMultiFoto($produksi, $request);

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil dicatat. Silakan setor setelah data final.');
    }

    public function setor(ProduksiTelur $produksi)
    {
        $user = auth()->user();
        if ($produksi->status_setor === 'sudah_disetor') {
            return redirect()->route('produksi.index')->with('error', 'Produksi ini sudah disetor.');
        }

        $kandang = $produksi->kandang;
        SetoranTelur::create([
            'produksi_telur_id' => $produksi->id,
            'gudang_id' => $kandang->gudang_id,
            'kandang_id' => $kandang->id,
            'tanggal_setor' => now(),
            'karpet' => floor($produksi->jumlah_butir / 30),
            'butir' => $produksi->jumlah_butir,
            'selisih' => 0,
            'input_by' => $user->id,
        ]);

        $produksi->update(['status_setor' => 'sudah_disetor']);

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil disetor ke gudang.');
    }

    public function show(ProduksiTelur $produksi)
    {
        $produksi->load(['kandang', 'user', 'fotos']);
        return view('produksi.show', compact('produksi'));
    }

    public function edit(ProduksiTelur $produksi)
    {
        $user = auth()->user();
        if ($produksi->status_setor === 'sudah_disetor' && !$user->hasRole('super_admin')) {
            return redirect()->route('produksi.index')->with('error', 'Data produksi yang sudah disetor tidak dapat diedit.');
        }

        $kandangs = $this->getUserKandangs()->where('status', 'aktif')->get();
        return view('produksi.edit', compact('produksi', 'kandangs'));
    }

    public function update(StoreProduksiTelurRequest $request, ProduksiTelur $produksi)
    {
        $user = auth()->user();
        if ($produksi->status_setor === 'sudah_disetor' && !$user->hasRole('super_admin')) {
            return redirect()->route('produksi.index')->with('error', 'Data produksi yang sudah disetor tidak dapat diedit.');
        }

        $data = $request->validated();
        unset($data['foto_base64'], $data['foto']);
        $data['sisa'] = (int) ($data['sisa'] ?? 0);
        $data['jumlah_butir'] = ($data['karpet'] * 30) + $data['sisa'];

        if ($request->filled('hapus_foto_ids')) {
            foreach ($request->hapus_foto_ids as $fid) {
                $f = $produksi->fotos()->find($fid);
                if ($f) {
                    Storage::disk('public')->delete($f->path);
                    $f->delete();
                }
            }
        }

        $this->simpanMultiFoto($produksi, $request);

        $produksi->update($data);
        return redirect()->route('produksi.index')->with('success', 'Data produksi telur berhasil diperbarui.');
    }

    public function destroy(ProduksiTelur $produksi)
    {
        $user = auth()->user();
        if ($produksi->status_setor === 'sudah_disetor' && !$user->hasRole('super_admin')) {
            return redirect()->route('produksi.index')->with('error', 'Data produksi yang sudah disetor tidak dapat dihapus.');
        }

        foreach ($produksi->fotos as $f) {
            Storage::disk('public')->delete($f->path);
        }
        $produksi->delete();
        return redirect()->route('produksi.index')->with('success', 'Data produksi telur berhasil dihapus.');
    }

    private function simpanMultiFoto(ProduksiTelur $produksi, $request): void
    {
        if ($request->filled('foto_base64')) {
            foreach ($request->foto_base64 as $b64) {
                if ($b64) {
                    $produksi->fotos()->create(['path' => $this->simpanFotoBase64($b64)]);
                }
            }
        }
    }

    private function simpanFotoBase64(string $base64): string
    {
        $img = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        $path = 'produksi/' . uniqid() . '.jpg';
        Storage::disk('public')->put($path, $img);
        return $path;
    }
}
