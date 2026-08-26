<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Http\Requests\StoreObatRequest;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $jenis = $request->get('jenis');

        $obats = Obat::with(['stok', 'pemakaian'])
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%")
                ->orWhere('kode', 'like', "%{$search}%"))
            ->when($jenis, fn($q) => $q->where('jenis', $jenis))
            ->orderBy('nama')
            ->paginate(10)->withQueryString();

        return view('obat.index', compact('obats', 'search', 'jenis'));
    }

    public function create()
    {
        return view('obat.create');
    }

    public function store(StoreObatRequest $request)
    {
        Obat::create($request->validated());
        return redirect()->route('obat.index')->with('success', 'Obat berhasil ditambahkan.');
    }

    public function edit(Obat $obat)
    {
        return view('obat.edit', compact('obat'));
    }

    public function update(StoreObatRequest $request, Obat $obat)
    {
        $obat->update($request->validated());
        return redirect()->route('obat.index')->with('success', 'Obat berhasil diperbarui.');
    }

    public function destroy(Obat $obat)
    {
        if ($obat->stok()->exists() || $obat->pemakaian()->exists()) {
            return back()->with('error', 'Obat tidak dapat dihapus karena sudah memiliki transaksi.');
        }
        $obat->delete();
        return redirect()->route('obat.index')->with('success', 'Obat berhasil dihapus.');
    }
}
