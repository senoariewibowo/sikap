<?php

namespace App\Http\Controllers;

use App\Models\BahanPakan;
use App\Models\BahanPakanStok;
use App\Models\BahanPakanStokLog;
use Illuminate\Http\Request;

class BahanPakanStokController extends Controller
{
    public function index(Request $request)
    {
        $gudangId = $request->get('gudang_id');
        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();

        $query = BahanPakanStok::with(['bahanPakan', 'gudang', 'user', 'logs']);

        $gudangIds = $this->getUserGudangIds();
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && !empty($gudangIds)) {
            $query->whereIn('gudang_id', $gudangIds);
        }

        if ($gudangId) {
            $query->where('gudang_id', $gudangId);
        }

        $stoks = $query->paginate(15)->withQueryString();

        return view('pakan.bahan.stok.index', compact('stoks', 'gudangs', 'gudangId'));
    }

    public function create()
    {
        $bahans = BahanPakan::where('status', 'aktif')->orderBy('nama')->get();
        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();
        return view('pakan.bahan.stok.create', compact('bahans', 'gudangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bahan_pakan_id' => 'required|exists:bahan_pakan,id',
            'gudang_id' => 'required|exists:gudang,id',
            'jumlah' => 'required|numeric|min:0.01',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        $stok = BahanPakanStok::where('bahan_pakan_id', $validated['bahan_pakan_id'])
            ->where('gudang_id', $validated['gudang_id'])
            ->first();

        $jumlahLama = $stok ? (float) $stok->jumlah : 0;
        $jumlahBaru = (float) $validated['jumlah'];
        $total = $jumlahLama + $jumlahBaru;

        if (!$stok) {
            $stok = BahanPakanStok::create([
                'bahan_pakan_id' => $validated['bahan_pakan_id'],
                'gudang_id' => $validated['gudang_id'],
                'jumlah' => $total,
                'tanggal' => $validated['tanggal'],
                'keterangan' => $validated['keterangan'],
                'created_by' => $validated['created_by'],
            ]);
        } else {
            $stok->update([
                'jumlah' => $total,
                'keterangan' => $validated['keterangan'],
            ]);
        }

        BahanPakanStokLog::create([
            'bahan_pakan_stok_id' => $stok->id,
            'jumlah_lama' => $jumlahLama,
            'jumlah_baru' => $jumlahBaru,
            'total' => $total,
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'],
            'created_by' => $validated['created_by'],
        ]);

        $bahan = $stok->bahanPakan;
        $gudang = $stok->gudang->nama_gudang;

        return redirect()->route('pakan.bahan.stok.index')
            ->with('success', "Stok {$bahan->nama} di {$gudang}: {$jumlahLama} → {$total} (+{$jumlahBaru} {$bahan->satuan}).");
    }

    public function riwayat(BahanPakanStok $stok)
    {
        $logs = $stok->logs()->with('user')->paginate(20);
        return view('pakan.bahan.stok.riwayat', compact('stok', 'logs'));
    }
}
