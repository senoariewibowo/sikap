<?php

namespace App\Http\Controllers;

use App\Models\PakanStok;
use App\Models\PakanStokLog;
use App\Models\Pakan;
use App\Http\Requests\StorePakanStokRequest;
use Illuminate\Http\Request;

class PakanStokController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $gudangId = $request->get('gudang_id');

        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();

        $query = PakanStok::with(['pakan', 'gudang', 'user', 'logs']);

        $gudangIds = $this->getUserGudangIds();
        if (!$user->hasRole('super_admin') && !empty($gudangIds)) {
            $query->whereIn('gudang_id', $gudangIds);
        }

        if ($gudangId) {
            $query->where('gudang_id', $gudangId);
        }

        $stoks = $query->paginate(15)->withQueryString();

        return view('pakan.stok.index', compact('stoks', 'gudangs', 'gudangId'));
    }

    public function create()
    {
        $pakan = Pakan::where('status', 'aktif')->orderBy('nama')->get();
        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();
        return view('pakan.stok.create', compact('pakan', 'gudangs'));
    }

    public function store(StorePakanStokRequest $request)
    {
        $stok = PakanStok::where('pakan_id', $request->pakan_id)
            ->where('gudang_id', $request->gudang_id)
            ->first();

        $jumlahLama = $stok ? $stok->jumlah : 0;
        $jumlahBaru = $request->jumlah;
        $total = $jumlahLama + $jumlahBaru;

        if (!$stok) {
            $stok = PakanStok::create([
                'pakan_id' => $request->pakan_id,
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

        PakanStokLog::create([
            'pakan_stok_id' => $stok->id,
            'jumlah_lama' => $jumlahLama,
            'jumlah_baru' => $jumlahBaru,
            'total' => $total,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        $pakan = Pakan::find($request->pakan_id);
        $gudang = $stok->gudang->nama_gudang;
        return redirect()->route('pakan.stok.index')
            ->with('success', "Stok {$pakan->nama} di {$gudang}: {$jumlahLama} → {$total} (+{$jumlahBaru} {$pakan->satuan}).");
    }

    public function riwayat(PakanStok $stok)
    {
        $logs = $stok->logs()->with('user')->paginate(20);
        return view('pakan.stok.riwayat', compact('stok', 'logs'));
    }
}
