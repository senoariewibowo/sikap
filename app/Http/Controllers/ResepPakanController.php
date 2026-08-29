<?php

namespace App\Http\Controllers;

use App\Models\ResepPakan;
use App\Models\ResepPakanDetail;
use App\Models\Pakan;
use App\Models\BahanPakan;
use App\Models\ProduksiPakan;
use Illuminate\Http\Request;

class ResepPakanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $reseps = ResepPakan::with(['pakan', 'details'])
            ->when($search, fn($q) => $q->where('nama_resep', 'like', "%{$search}%")
                ->orWhereHas('pakan', fn($q2) => $q2->where('nama', 'like', "%{$search}%")))
            ->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString();

        return view('pakan.resep.index', compact('reseps', 'search'));
    }

    public function create()
    {
        $pakans = Pakan::where('status', 'aktif')->orderBy('nama')->get();
        $bahans = BahanPakan::where('status', 'aktif')->orderBy('nama')->get();
        return view('pakan.resep.create', compact('pakans', 'bahans'));
    }

    public function ajaxResepByPakan(Pakan $pakan)
    {
        $reseps = ResepPakan::where('pakan_id', $pakan->id)
            ->where('status', 'aktif')
            ->orderBy('is_default', 'desc')
            ->orderBy('nama_resep')
            ->get(['id', 'nama_resep', 'is_default']);
        return response()->json($reseps);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pakan_id' => 'required|exists:pakan,id',
            'nama_resep' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'details' => 'required|array|min:1',
            'details.*.bahan_pakan_id' => 'required|distinct|exists:bahan_pakan,id',
            'details.*.jumlah' => 'required|numeric|min:0.01',
            'details.*.catatan' => 'nullable|string',
        ]);

        if (!empty($validated['is_default'])) {
            ResepPakan::where('pakan_id', $validated['pakan_id'])->update(['is_default' => false]);
        }

        $resep = ResepPakan::create([
            'pakan_id' => $validated['pakan_id'],
            'nama_resep' => $validated['nama_resep'],
            'is_default' => !empty($validated['is_default']),
            'keterangan' => $validated['keterangan'] ?? null,
            'status' => $validated['status'],
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['details'] as $d) {
            $resep->details()->create([
                'bahan_pakan_id' => $d['bahan_pakan_id'],
                'jumlah' => $d['jumlah'],
                'catatan' => $d['catatan'] ?? null,
            ]);
        }

        return redirect()->route('pakan.resep.index')->with('success', 'Resep pakan berhasil ditambahkan.');
    }

    public function edit(ResepPakan $resep)
    {
        $resep->load('details.bahanPakan');
        $pakans = Pakan::where('status', 'aktif')->orderBy('nama')->get();
        $bahans = BahanPakan::where('status', 'aktif')->orderBy('nama')->get();
        return view('pakan.resep.edit', compact('resep', 'pakans', 'bahans'));
    }

    public function update(Request $request, ResepPakan $resep)
    {
        $validated = $request->validate([
            'pakan_id' => 'required|exists:pakan,id',
            'nama_resep' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'details' => 'required|array|min:1',
            'details.*.bahan_pakan_id' => 'required|distinct|exists:bahan_pakan,id',
            'details.*.jumlah' => 'required|numeric|min:0.01',
            'details.*.catatan' => 'nullable|string',
        ]);

        if (!empty($validated['is_default'])) {
            ResepPakan::where('pakan_id', $validated['pakan_id'])
                ->where('id', '!=', $resep->id)
                ->update(['is_default' => false]);
        }

        $resep->update([
            'pakan_id' => $validated['pakan_id'],
            'nama_resep' => $validated['nama_resep'],
            'is_default' => !empty($validated['is_default']),
            'keterangan' => $validated['keterangan'] ?? null,
            'status' => $validated['status'],
        ]);

        $resep->details()->delete();

        foreach ($validated['details'] as $d) {
            $resep->details()->create([
                'bahan_pakan_id' => $d['bahan_pakan_id'],
                'jumlah' => $d['jumlah'],
                'catatan' => $d['catatan'] ?? null,
            ]);
        }

        return redirect()->route('pakan.resep.index')->with('success', 'Resep pakan berhasil diperbarui.');
    }

    public function destroy(ResepPakan $resep)
    {
        if (ProduksiPakan::where('resep_pakan_id', $resep->id)->exists()) {
            return back()->with('error', 'Resep tidak dapat dihapus karena sudah digunakan dalam produksi.');
        }

        $resep->details()->delete();
        $resep->delete();

        return redirect()->route('pakan.resep.index')->with('success', 'Resep pakan berhasil dihapus.');
    }
}
