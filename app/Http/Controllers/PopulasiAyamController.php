<?php

namespace App\Http\Controllers;

use App\Models\PopulasiAyam;
use App\Models\Kandang;
use App\Http\Requests\StorePopulasiAyamRequest;
use Illuminate\Http\Request;

class PopulasiAyamController extends Controller
{
    public function index(Request $request)
    {
        $kandangId = $request->get('kandang_id');
        $tanggalStart = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $tanggalEnd = $request->get('sampai', now()->format('Y-m-d'));

        $query = PopulasiAyam::with(['kandang', 'user'])->forUser()
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        if ($kandangId) {
            $query->where('kandang_id', $kandangId);
        }

        $query->whereBetween('tanggal', [$tanggalStart, $tanggalEnd]);
        $populasis = $query->paginate(15)->withQueryString();

        $kandangs = $this->getUserKandangs()->get();

        $rekapPerKandang = $this->scopeByUser(
            PopulasiAyam::selectRaw('
                kandang_id,
                SUM(jumlah_masuk) as total_masuk,
                SUM(jumlah_mati) as total_mati,
                SUM(jumlah_afkir) as total_afkir
            ')
        )
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->groupBy('kandang_id')
            ->with('kandang')
            ->get();

        return view('populasi.index', compact(
            'populasis', 'kandangs', 'kandangId', 'tanggalStart', 'tanggalEnd', 'rekapPerKandang'
        ));
    }

    public function create()
    {
        $kandangs = $this->getUserKandangs()->where('status', 'aktif')->get();
        return view('populasi.create', compact('kandangs'));
    }

    public function store(StorePopulasiAyamRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        PopulasiAyam::create($data);

        return redirect()->route('populasi.index')
            ->with('success', 'Data populasi berhasil dicatat.');
    }

    public function show(PopulasiAyam $populasi)
    {
        $populasi->load(['kandang', 'user']);
        return view('populasi.show', compact('populasi'));
    }

    public function edit(PopulasiAyam $populasi)
    {
        $kandangs = $this->getUserKandangs()->where('status', 'aktif')->get();
        return view('populasi.edit', compact('populasi', 'kandangs'));
    }

    public function update(StorePopulasiAyamRequest $request, PopulasiAyam $populasi)
    {
        $populasi->update($request->validated());

        return redirect()->route('populasi.index')
            ->with('success', 'Data populasi berhasil diperbarui.');
    }

    public function destroy(PopulasiAyam $populasi)
    {
        $populasi->delete();

        return redirect()->route('populasi.index')
            ->with('success', 'Data populasi berhasil dihapus.');
    }

    public function mutasi(Request $request)
    {
        $kandangs = $this->getUserKandangs()->where('status', 'aktif')->get();

        if ($request->isMethod('post')) {
            $request->validate([
                'kandang_asal_id' => 'required|exists:kandang,id|different:kandang_tujuan_id',
                'kandang_tujuan_id' => 'required|exists:kandang,id',
                'jumlah' => 'required|integer|min:1',
                'tanggal' => 'required|date',
                'keterangan' => 'nullable|string|max:500',
            ]);

            $source = $this->getUserKandangs()->findOrFail($request->kandang_asal_id);
            $target = $this->getUserKandangs()->findOrFail($request->kandang_tujuan_id);

            $maxMutasi = $source->populasiSekarang();
            if ($request->jumlah > $maxMutasi) {
                return back()->with('error', "Populasi di {$source->nama_kandang} hanya {$maxMutasi} ekor. Mutasi melebihi populasi tersedia.")->withInput();
            }

            PopulasiAyam::create([
                'kandang_id' => $request->kandang_asal_id,
                'tanggal' => $request->tanggal,
                'jumlah_afkir' => $request->jumlah,
                'keterangan' => "Mutasi ke {$target->nama_kandang} - " . $request->keterangan,
                'created_by' => auth()->id(),
            ]);

            PopulasiAyam::create([
                'kandang_id' => $request->kandang_tujuan_id,
                'tanggal' => $request->tanggal,
                'jumlah_masuk' => $request->jumlah,
                'keterangan' => "Mutasi dari {$source->nama_kandang} - " . $request->keterangan,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('populasi.index')
                ->with('success', "Mutasi {$request->jumlah} ekor berhasil dicatat.");
        }

        return view('populasi.mutasi', compact('kandangs'));
    }
}
