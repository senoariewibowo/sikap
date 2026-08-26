<?php

namespace App\Http\Controllers;

use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;

class KategoriPengeluaranController extends Controller
{
    public function index()
    {
        $kategoris = KategoriPengeluaran::orderBy('nama')->paginate(10);
        return view('keuangan.kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('keuangan.kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100|unique:kategori_pengeluaran,nama']);
        KategoriPengeluaran::create($request->all());
        return redirect()->route('keuangan.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(KategoriPengeluaran $kategori)
    {
        return view('keuangan.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriPengeluaran $kategori)
    {
        $request->validate(['nama' => 'required|string|max:100|unique:kategori_pengeluaran,nama,' . $kategori->id]);
        $kategori->update($request->all());
        return redirect()->route('keuangan.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriPengeluaran $kategori)
    {
        if ($kategori->pengeluaran()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus: masih ada data pengeluaran.');
        }
        $kategori->delete();
        return redirect()->route('keuangan.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
