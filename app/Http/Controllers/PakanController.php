<?php

namespace App\Http\Controllers;

use App\Models\Pakan;
use App\Models\Gudang;
use App\Http\Requests\StorePakanRequest;
use Illuminate\Http\Request;

class PakanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $gudangId = $request->get('gudang_id');

        $gudangs = Gudang::where('status', 'aktif')->orderBy('nama_gudang')->get();

        $pakans = Pakan::with(['stok', 'distribusi', 'pemakaian'])
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%")
                ->orWhere('kode', 'like', "%{$search}%"))
            ->orderBy('nama')
            ->paginate(10)->withQueryString();

        return view('pakan.master.index', compact('pakans', 'gudangs', 'gudangId', 'search'));
    }

    public function create()
    {
        $nextKode = 'PAK-' . str_pad(Pakan::max('id') + 1, 3, '0', STR_PAD_LEFT);
        return view('pakan.master.create', compact('nextKode'));
    }

    public function store(StorePakanRequest $request)
    {
        $data = $request->validated();
        $data['kode'] = 'PAK-' . str_pad(Pakan::max('id') + 1, 3, '0', STR_PAD_LEFT);

        Pakan::create($data);
        return redirect()->route('pakan.index')->with('success', 'Pakan berhasil ditambahkan.');
    }

    public function edit(Pakan $pakan)
    {
        return view('pakan.master.edit', compact('pakan'));
    }

    public function update(StorePakanRequest $request, Pakan $pakan)
    {
        $pakan->update($request->validated());
        return redirect()->route('pakan.index')->with('success', 'Pakan berhasil diperbarui.');
    }

    public function destroy(Pakan $pakan)
    {
        if ($pakan->stok()->exists() || $pakan->distribusi()->exists() || $pakan->pemakaian()->exists()) {
            return back()->with('error', 'Pakan tidak dapat dihapus karena sudah memiliki transaksi.');
        }
        $pakan->delete();
        return redirect()->route('pakan.index')->with('success', 'Pakan berhasil dihapus.');
    }
}
