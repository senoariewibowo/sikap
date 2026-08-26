<?php

namespace App\Http\Controllers;

use App\Models\JenisPakan;
use App\Http\Requests\StoreJenisPakanRequest;
use Illuminate\Http\Request;

class JenisPakanController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->get('kategori', 'pakan');
        $search = $request->get('search');
        $jenisPakans = JenisPakan::when($kategori !== 'semua', fn($q) => $q->whereIn('kategori', $this->kategoriMap($kategori)))
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%"))
            ->orderBy('nama')->paginate(10)->withQueryString();
        $kategoriLabel = $kategori === 'obat' ? 'Obat & Vitamin' : ucfirst($kategori);
        return view('pakan.jenis.index', compact('jenisPakans', 'search', 'kategori', 'kategoriLabel'));
    }

    public function create(Request $request)
    {
        $defaultKategori = $request->get('kategori', 'pakan');
        return view('pakan.jenis.create', compact('defaultKategori'));
    }

    public function store(StoreJenisPakanRequest $request)
    {
        JenisPakan::create($request->validated());
        return redirect()->route('pakan.jenis.index', ['kategori' => $request->kategori])->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(JenisPakan $jenisPakan)
    {
        return view('pakan.jenis.edit', compact('jenisPakan'));
    }

    public function update(StoreJenisPakanRequest $request, JenisPakan $jenisPakan)
    {
        $jenisPakan->update($request->validated());
        return redirect()->route('pakan.jenis.index', ['kategori' => $jenisPakan->kategori])->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(JenisPakan $jenisPakan)
    {
        if ($jenisPakan->stokPakan()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus: masih ada riwayat stok.');
        }
        $jenisPakan->delete();
        return redirect()->route('pakan.jenis.index', ['kategori' => $jenisPakan->kategori])->with('success', 'Data berhasil dihapus.');
    }

    private function kategoriMap(string $kategori): array
    {
        return $kategori === 'obat' ? ['obat', 'vitamin'] : [$kategori];
    }
}
