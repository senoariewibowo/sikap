<?php

namespace App\Http\Controllers;

use App\Models\PakanDistribusi;
use App\Models\Pakan;
use App\Models\Kandang;
use App\Http\Requests\StorePakanDistribusiRequest;
use Illuminate\Http\Request;

class PakanDistribusiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $gudangId = $request->get('gudang_id');
        $status = $request->get('status');

        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();

        $query = PakanDistribusi::with(['pakan', 'gudang', 'kandang', 'user', 'penerima'])
            ->orderBy('tanggal_kirim', 'desc')
            ->orderBy('id', 'desc');

        $gudangIds = $this->getUserGudangIds();
        if (!$user->hasRole('super_admin') && !empty($gudangIds)) {
            $query->whereIn('gudang_id', $gudangIds);
        }

        if ($gudangId) {
            $query->where('gudang_id', $gudangId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $distribusis = $query->paginate(15)->withQueryString();

        return view('pakan.distribusi.index', compact('distribusis', 'gudangs', 'gudangId', 'status'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        $pakan = Pakan::where('status', 'aktif')->orderBy('nama')->get();
        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();

        $gudangId = $request->get('gudang_id', $user->hasRole('petugas_gudang') ? $user->gudang_id : null);

        $kandangs = collect();
        if ($gudangId) {
            $kandangs = Kandang::where('gudang_id', $gudangId)
                ->where('status', 'aktif')
                ->orderBy('nama_kandang')
                ->get();
        }

        return view('pakan.distribusi.create', compact('pakan', 'gudangs', 'kandangs', 'gudangId'));
    }

    public function store(StorePakanDistribusiRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['status'] = 'dikirim';

        $pakan = Pakan::find($request->pakan_id);
        $stokGudang = $pakan->stokGudang($request->gudang_id);

        if ($request->jumlah > $stokGudang) {
            return back()->withInput()->with('error', "Stok gudang tidak mencukupi. Tersedia: {$stokGudang} {$pakan->satuan}.");
        }

        PakanDistribusi::create($data);

        return redirect()->route('pakan.distribusi.index')->with('success', "Distribusi {$pakan->nama} ke kandang berhasil.");
    }

    public function destroy(PakanDistribusi $distribusi)
    {
        if ($distribusi->status === 'diterima') {
            return back()->with('error', 'Distribusi yang sudah diterima tidak dapat dihapus.');
        }

        $distribusi->delete();
        return back()->with('success', 'Distribusi berhasil dihapus.');
    }

    public function ajaxStok(Request $request)
    {
        $pakanId = $request->get('pakan_id');
        $gudangId = $request->get('gudang_id');

        if (!$pakanId || !$gudangId) {
            return response()->json(['stok' => null]);
        }

        $pakan = Pakan::find($pakanId);
        if (!$pakan) {
            return response()->json(['stok' => null]);
        }

        return response()->json([
            'stok' => $pakan->stokGudang($gudangId),
            'satuan' => $pakan->satuan,
        ]);
    }
}
