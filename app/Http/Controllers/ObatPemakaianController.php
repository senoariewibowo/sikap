<?php

namespace App\Http\Controllers;

use App\Models\ObatPemakaian;
use App\Models\Obat;
use App\Http\Requests\StoreObatPemakaianRequest;
use Illuminate\Http\Request;

class ObatPemakaianController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $kandangAktif = $user->karyawan?->kandangAktif()->get() ?? collect();
        $kandangId = $request->get('kandang_id', $kandangAktif->first()?->id);

        $pemakaians = ObatPemakaian::with(['obat', 'kandang', 'user'])
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->when($kandangAktif->isNotEmpty(), fn($q) => $q->whereIn('kandang_id', $kandangAktif->pluck('id')))
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)->withQueryString();

        $obats = Obat::where('status', 'aktif')->orderBy('nama')->get();

        return view('obat.pemakaian.index', compact('kandangAktif', 'kandangId', 'pemakaians', 'obats'));
    }

    public function store(StoreObatPemakaianRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        ObatPemakaian::create($data);

        $obat = Obat::find($request->obat_id);
        return redirect()->route('obat.pemakaian.index', ['kandang_id' => $request->kandang_id])
            ->with('success', "Pemakaian {$obat->nama} berhasil dicatat.");
    }
}
