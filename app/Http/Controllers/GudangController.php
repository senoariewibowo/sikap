<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index()
    {
        $gudangs = Gudang::orderBy('nama_gudang')->paginate(10);
        return view('gudang.index', compact('gudangs'));
    }

    public function create()
    {
        $nextKode = 'GDG-' . str_pad(\App\Models\Gudang::count() + 1, 3, '0', STR_PAD_LEFT);
        return view('gudang.create', compact('nextKode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $count = \App\Models\Gudang::count() + 1;
        $validated['kode_gudang'] = 'GDG-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        Gudang::create($validated);

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function edit(Gudang $gudang)
    {
        return view('gudang.edit', compact('gudang'));
    }

    public function update(Request $request, Gudang $gudang)
    {
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $gudang->update($validated);

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Gudang $gudang)
    {
        $gudang->delete();
        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil dihapus.');
    }
}
