<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\Kandang;
use App\Exports\PengeluaranExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $kategoriId = $request->get('kategori_id');
        $kandangId = $request->get('kandang_id');
        $search = $request->get('search');
        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $query = Pengeluaran::with(['kategori', 'kandang', 'user'])
            ->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if ($kategoriId) $query->where('kategori_pengeluaran_id', $kategoriId);
        if ($kandangId) $query->where('kandang_id', $kandangId);
        if ($search) $query->where('keterangan', 'like', "%{$search}%");
        $query->whereBetween('tanggal', [$dari, $sampai]);

        $pengeluarans = $query->paginate(15)->withQueryString();

        $total = Pengeluaran::when($kategoriId, fn($q) => $q->where('kategori_pengeluaran_id', $kategoriId))
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])->sum('jumlah');

        $perKategori = Pengeluaran::selectRaw('kategori_pengeluaran_id, SUM(jumlah) as total')
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->groupBy('kategori_pengeluaran_id')->with('kategori')->get();

        $kategoris = KategoriPengeluaran::orderBy('nama')->get();
        $kandangs = Kandang::orderBy('nama_kandang')->get();

        return view('keuangan.pengeluaran.index', compact(
            'pengeluarans', 'kategoris', 'kandangs', 'kategoriId', 'kandangId',
            'search', 'dari', 'sampai', 'total', 'perKategori'
        ));
    }

    public function create()
    {
        $kategoris = KategoriPengeluaran::orderBy('nama')->get();
        $kandangs = Kandang::orderBy('nama_kandang')->get();
        return view('keuangan.pengeluaran.create', compact('kategoris', 'kandangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
            'kandang_id' => 'nullable|exists:kandang,id',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('pengeluaran', 'public');
        }

        Pengeluaran::create($data);
        return redirect()->route('keuangan.pengeluaran.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function edit(Pengeluaran $pengeluaran)
    {
        $kategoris = KategoriPengeluaran::orderBy('nama')->get();
        $kandangs = Kandang::orderBy('nama_kandang')->get();
        return view('keuangan.pengeluaran.edit', compact('pengeluaran', 'kategoris', 'kandangs'));
    }

    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
            'kandang_id' => 'nullable|exists:kandang,id',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('bukti')) {
            if ($pengeluaran->bukti) {
                Storage::disk('public')->delete($pengeluaran->bukti);
            }
            $data['bukti'] = $request->file('bukti')->store('pengeluaran', 'public');
        }

        $pengeluaran->update($data);
        return redirect()->route('keuangan.pengeluaran.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        if ($pengeluaran->bukti) {
            Storage::disk('public')->delete($pengeluaran->bukti);
        }
        $pengeluaran->delete();
        return redirect()->route('keuangan.pengeluaran.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PengeluaranExport($request->kategori_id, $request->kandang_id, $request->dari ?? now()->startOfMonth()->format('Y-m-d'), $request->sampai ?? now()->format('Y-m-d')),
            'Laporan_Pengeluaran.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $kategoriId = $request->get('kategori_id');
        $kandangId = $request->get('kandang_id');
        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $data = Pengeluaran::with(['kategori', 'kandang'])
            ->when($kategoriId, fn($q) => $q->where('kategori_pengeluaran_id', $kategoriId))
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')->get();

        $pdf = PDF::loadView('keuangan.pengeluaran.pdf', compact('data', 'dari', 'sampai'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("Laporan_Pengeluaran_{$dari}_{$sampai}.pdf");
    }
}
