<?php

namespace App\Http\Controllers;

use App\Models\Kandang;
use App\Models\ProduksiTelur;
use App\Models\PopulasiAyam;
use App\Models\StokPakan;
use App\Models\JenisPakan;
use App\Exports\ProduksiExport;
use App\Exports\MortalitasExport;
use App\Exports\PakanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function produksi(Request $request)
    {
        $kandangId = $request->get('kandang_id');
        $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $kandangs = $this->getUserKandangs()->get();

        $data = $this->scopeByUser(ProduksiTelur::with('kandang'))
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')
            ->paginate(20)->withQueryString();

        $summary = $this->scopeByUser(ProduksiTelur::selectRaw('SUM(jumlah_butir) as total_butir'))
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])->first();

        return view('laporan.produksi', compact(
            'data', 'kandangs', 'kandangId', 'dari', 'sampai', 'summary'
        ));
    }

    public function exportProduksiExcel(Request $request)
    {
        $kandangId = $request->get('kandang_id');
        $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        return Excel::download(
            new ProduksiExport($kandangId, $dari, $sampai),
            "Laporan_Produksi_{$dari}_sampai_{$sampai}.xlsx"
        );
    }

    public function exportProduksiPdf(Request $request)
    {
        $kandangId = $request->get('kandang_id');
        $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $data = $this->scopeByUser(ProduksiTelur::with('kandang'))
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')
            ->get();

        $summary = $this->scopeByUser(ProduksiTelur::selectRaw('SUM(jumlah_butir) as total_butir, SUM(berat_kg) as total_kg'))
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->first();

        $pdf = PDF::loadView('laporan.pdf.produksi', compact('data', 'dari', 'sampai', 'summary'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("Laporan_Produksi_{$dari}_{$sampai}.pdf");
    }

    public function mortalitas(Request $request)
    {
        $kandangId = $request->get('kandang_id');
        $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $kandangs = $this->getUserKandangs()->get();

        $data = $this->scopeByUser(PopulasiAyam::with('kandang'))
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->where(function ($q) {
                $q->where('jumlah_mati', '>', 0)->orWhere('jumlah_afkir', '>', 0);
            })
            ->orderBy('tanggal')
            ->paginate(20)->withQueryString();

        $summary = $this->scopeByUser(PopulasiAyam::selectRaw('SUM(jumlah_mati) as total_mati, SUM(jumlah_afkir) as total_afkir'))
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])->first();

        $byKandang = $this->scopeByUser(PopulasiAyam::selectRaw('kandang_id, SUM(jumlah_mati) as total_mati, SUM(jumlah_afkir) as total_afkir'))
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])->groupBy('kandang_id')->with('kandang')->get();

        return view('laporan.mortalitas', compact(
            'data', 'kandangs', 'kandangId', 'dari', 'sampai', 'summary', 'byKandang'
        ));
    }

    public function exportMortalitasExcel(Request $request)
    {
        return Excel::download(
            new MortalitasExport($request->kandang_id, $request->dari ?? now()->subDays(30)->format('Y-m-d'), $request->sampai ?? now()->format('Y-m-d')),
            'Laporan_Mortalitas.xlsx'
        );
    }

    public function exportMortalitasPdf(Request $request)
    {
        $kandangId = $request->get('kandang_id');
        $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $data = $this->scopeByUser(PopulasiAyam::with('kandang'))
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->where(function ($q) {
                $q->where('jumlah_mati', '>', 0)->orWhere('jumlah_afkir', '>', 0);
            })
            ->orderBy('tanggal')
            ->get();

        $pdf = PDF::loadView('laporan.pdf.mortalitas', compact('data', 'dari', 'sampai'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("Laporan_Mortalitas_{$dari}_{$sampai}.pdf");
    }

    public function pakan(Request $request)
    {
        $kandangId = $request->get('kandang_id');
        $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $kandangs = $this->getUserKandangs()->get();

        $data = StokPakan::with(['jenisPakan', 'kandang'])
            ->when(!auth()->user()->hasRole('super_admin'), function ($q) {
                $ids = $this->getUserKandangIds();
                $q->where(function ($q2) use ($ids) {
                    $q2->whereIn('kandang_id', $ids)->orWhereNull('kandang_id');
                });
            })
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')
            ->paginate(20)->withQueryString();

        $summaryMasuk = StokPakan::where('tipe', 'masuk')
            ->when(!auth()->user()->hasRole('super_admin'), function ($q) {
                $ids = $this->getUserKandangIds();
                $q->where(function ($q2) use ($ids) {
                    $q2->whereIn('kandang_id', $ids)->orWhereNull('kandang_id');
                });
            })
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])->sum('jumlah_kg');

        $summaryKeluar = StokPakan::where('tipe', 'keluar')
            ->when(!auth()->user()->hasRole('super_admin'), function ($q) {
                $ids = $this->getUserKandangIds();
                $q->where(function ($q2) use ($ids) {
                    $q2->whereIn('kandang_id', $ids)->orWhereNull('kandang_id');
                });
            })
            ->whereBetween('tanggal', [$dari, $sampai])
            ->sum('jumlah_kg');

        $byJenis = StokPakan::selectRaw('jenis_pakan_id, tipe, SUM(jumlah_kg) as total')
            ->when(!auth()->user()->hasRole('super_admin'), function ($q) {
                $ids = $this->getUserKandangIds();
                $q->where(function ($q2) use ($ids) {
                    $q2->whereIn('kandang_id', $ids)->orWhereNull('kandang_id');
                });
            })
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->groupBy('jenis_pakan_id', 'tipe')
            ->with('jenisPakan')
            ->get();

        return view('laporan.pakan', compact(
            'data', 'kandangs', 'kandangId', 'dari', 'sampai', 'summaryMasuk', 'summaryKeluar', 'byJenis'
        ));
    }

    public function exportPakanExcel(Request $request)
    {
        return Excel::download(
            new PakanExport($request->kandang_id, $request->dari ?? now()->subDays(30)->format('Y-m-d'), $request->sampai ?? now()->format('Y-m-d')),
            'Laporan_Pakan.xlsx'
        );
    }

    public function exportPakanPdf(Request $request)
    {
        $kandangId = $request->get('kandang_id');
        $dari = $request->get('dari', now()->subDays(30)->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $data = StokPakan::with(['jenisPakan', 'kandang'])
            ->when(!auth()->user()->hasRole('super_admin'), function ($q) {
                $ids = $this->getUserKandangIds();
                $q->where(function ($q2) use ($ids) {
                    $q2->whereIn('kandang_id', $ids)->orWhereNull('kandang_id');
                });
            })
            ->when($kandangId, fn($q) => $q->where('kandang_id', $kandangId))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')
            ->get();

        $pdf = PDF::loadView('laporan.pdf.pakan', compact('data', 'dari', 'sampai'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("Laporan_Pakan_{$dari}_{$sampai}.pdf");
    }

    public function peta()
    {
        $kandangs = Kandang::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', 'aktif')
            ->get();

        return view('peta.index', compact('kandangs'));
    }
}
