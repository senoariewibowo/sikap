<?php

namespace App\Http\Controllers;

use App\Models\StokTelurKeluar;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->hasRole('driver')) {
            abort(403);
        }

        $driverName = $user->karyawan?->nama;
        if (!$driverName) {
            return back()->with('error', 'Profil driver tidak lengkap.');
        }

        $sjs = StokTelurKeluar::with([
                'gudang',
                'details.sortasiDetail.sortasiTelur.kandang',
                'availableDetails.sortasiDetail.sortasiTelur.kandang',
                'penjualanStokDetails',
            ])
            ->where('driver', $driverName)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $latestSj = $sjs->first();

        $sjs = $sjs->map(function ($sj) use ($latestSj) {
            $availableDetails = $sj->availableDetails;
            $totalButir = $sj->details->sum('jumlah_butir');
            $soldButir = $sj->penjualanStokDetails->whereIn('stok_telur_keluar_detail_id', $availableDetails->pluck('id'))->sum('jumlah_butir');
            $sj->total_butir = $totalButir;
            $sj->sisa_butir = max(0, $availableDetails->sum('jumlah_butir') - $soldButir);
            $sj->total_peti = $sj->details->count();
            $soldDetailIds = $sj->penjualanStokDetails->pluck('stok_telur_keluar_detail_id')->unique();
            $sj->sisa_peti = $availableDetails->whereNotIn('id', $soldDetailIds)->count();
            $sj->has_sisa = $sj->sisa_butir > 0;
            $sj->is_latest = $latestSj && $latestSj->id === $sj->id;
            return $sj;
        });

        return view('transaksi.index', compact('sjs'));
    }

    public function suratJalan(StokTelurKeluar $stokTelurKeluar)
    {
        $user = auth()->user();
        if (!$user->hasRole('driver')) {
            abort(403);
        }

        $driverName = $user->karyawan?->nama;
        if (!$driverName || $stokTelurKeluar->driver !== $driverName) {
            abort(403);
        }

        $stokTelurKeluar->load(['gudang', 'user', 'details.sortasiDetail.sortasiTelur.kandang', 'ttdPengirim', 'ttdMengetahui']);
        $stokTelur = $stokTelurKeluar;
        return view('telur.keluar.surat-jalan', compact('stokTelur'));
    }

}
