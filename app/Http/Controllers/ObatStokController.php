<?php

namespace App\Http\Controllers;

use App\Models\ObatStok;
use App\Models\ObatStokLog;
use App\Models\Obat;
use App\Http\Requests\StoreObatStokRequest;
use Illuminate\Http\Request;

class ObatStokController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $gudangId = $request->get('gudang_id');

        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();

        $query = ObatStok::with(['obat', 'gudang', 'user', 'logs']);

        $gudangIds = $this->getUserGudangIds();
        if (!$user->hasRole('super_admin') && !empty($gudangIds)) {
            $query->whereIn('gudang_id', $gudangIds);
        }

        if ($gudangId) {
            $query->where('gudang_id', $gudangId);
        }

        $stoks = $query->paginate(15)->withQueryString();

        return view('obat.stok.index', compact('stoks', 'gudangs', 'gudangId'));
    }

    public function create()
    {
        $obats = Obat::where('status', 'aktif')->orderBy('nama')->get();
        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();
        return view('obat.stok.create', compact('obats', 'gudangs'));
    }

    public function store(StoreObatStokRequest $request)
    {
        $stok = ObatStok::where('obat_id', $request->obat_id)
            ->where('gudang_id', $request->gudang_id)
            ->first();

        $jumlahLama = $stok ? $stok->jumlah : 0;
        $jumlahBaru = $request->jumlah;
        $total = $jumlahLama + $jumlahBaru;

        if (!$stok) {
            $stok = ObatStok::create([
                'obat_id' => $request->obat_id,
                'gudang_id' => $request->gudang_id,
                'jumlah' => $total,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'created_by' => auth()->id(),
            ]);
        } else {
            $stok->update([
                'jumlah' => $total,
                'keterangan' => $request->keterangan,
            ]);
        }

        ObatStokLog::create([
            'obat_stok_id' => $stok->id,
            'jumlah_lama' => $jumlahLama,
            'jumlah_baru' => $jumlahBaru,
            'total' => $total,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        $obat = Obat::find($request->obat_id);
        $gudang = $stok->gudang->nama_gudang;
        return redirect()->route('obat.stok.index')
            ->with('success', "Stok {$obat->nama} di {$gudang}: {$jumlahLama} → {$total} (+{$jumlahBaru} {$obat->satuan}).");
    }

    public function riwayat(ObatStok $stok)
    {
        $logs = $stok->logs()->with('user')->paginate(20);
        return view('obat.stok.riwayat', compact('stok', 'logs'));
    }
}
