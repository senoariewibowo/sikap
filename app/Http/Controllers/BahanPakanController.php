<?php

namespace App\Http\Controllers;

use App\Models\BahanPakan;
use App\Models\ResepPakanDetail;
use App\Models\ProduksiPakanDetail;
use Illuminate\Http\Request;

class BahanPakanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $bahans = BahanPakan::when($search, fn($q) => $q->where('nama', 'like', "%{$search}%")
            ->orWhere('kode', 'like', "%{$search}%"))
            ->orderBy('nama')
            ->paginate(10)->withQueryString();

        return view('pakan.bahan.index', compact('bahans', 'search'));
    }

    public function create()
    {
        $nextKode = 'BHN-' . str_pad(BahanPakan::max('id') + 1, 3, '0', STR_PAD_LEFT);
        return view('pakan.bahan.create', compact('nextKode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'satuan' => 'required|string|max:20',
            'harga' => 'nullable|numeric|min:0',
            'stok_minimal' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $validated['kode'] = 'BHN-' . str_pad(BahanPakan::max('id') + 1, 3, '0', STR_PAD_LEFT);

        BahanPakan::create($validated);

        return redirect()->route('pakan.bahan.index')->with('success', 'Bahan pakan berhasil ditambahkan.');
    }

    public function edit(BahanPakan $bahan)
    {
        return view('pakan.bahan.edit', compact('bahan'));
    }

    public function update(Request $request, BahanPakan $bahan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'satuan' => 'required|string|max:20',
            'harga' => 'nullable|numeric|min:0',
            'stok_minimal' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $bahan->update($validated);

        return redirect()->route('pakan.bahan.index')->with('success', 'Bahan pakan berhasil diperbarui.');
    }

    public function destroy(BahanPakan $bahan)
    {
        if ($bahan->stok()->exists()) {
            return back()->with('error', 'Bahan pakan tidak dapat dihapus karena sudah memiliki stok.');
        }

        if (ResepPakanDetail::where('bahan_pakan_id', $bahan->id)->exists()) {
            return back()->with('error', 'Bahan pakan tidak dapat dihapus karena sudah digunakan dalam resep.');
        }

        if (ProduksiPakanDetail::where('bahan_pakan_id', $bahan->id)->exists()) {
            return back()->with('error', 'Bahan pakan tidak dapat dihapus karena sudah digunakan dalam produksi.');
        }

        $bahan->delete();

        return redirect()->route('pakan.bahan.index')->with('success', 'Bahan pakan berhasil dihapus.');
    }
}
