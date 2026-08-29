<?php

namespace App\Http\Controllers;

use App\Models\BahanPakan;
use App\Models\BahanPakanStok;
use App\Models\BahanPakanStokLog;
use App\Models\Gudang;
use App\Models\Pakan;
use App\Models\PakanStok;
use App\Models\PakanStokLog;
use App\Models\ProduksiPakan;
use App\Models\ProduksiPakanBiayaLain;
use App\Models\ProduksiPakanDetail;
use App\Models\ResepPakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduksiPakanController extends Controller
{
    public function index(Request $request)
    {
        $gudangId = $request->get('gudang_id');
        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();

        $query = ProduksiPakan::with(['pakan', 'resepPakan', 'gudang'])
            ->orderBy('tanggal', 'desc');

        $gudangIds = $this->getUserGudangIds();
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && !empty($gudangIds)) {
            $query->whereIn('gudang_id', $gudangIds);
        }

        if ($gudangId) {
            $query->where('gudang_id', $gudangId);
        }

        $produksis = $query->paginate(15)->withQueryString();

        return view('pakan.produksi.index', compact('produksis', 'gudangs', 'gudangId'));
    }

    public function create()
    {
        $pakans = Pakan::where('status', 'aktif')
            ->whereHas('resepPakan', fn($q) => $q->where('status', 'aktif'))
            ->orderBy('nama')
            ->get();
        $gudangs = $this->getUserGudangs()->where('status', 'aktif')->get();
        return view('pakan.produksi.create', compact('pakans', 'gudangs'));
    }

    public function ajaxResepByPakan(Pakan $pakan)
    {
        $reseps = ResepPakan::where('pakan_id', $pakan->id)
            ->where('status', 'aktif')
            ->orderBy('is_default', 'desc')
            ->orderBy('nama_resep')
            ->get(['id', 'nama_resep', 'is_default']);
        return response()->json($reseps);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'pakan_id' => 'required|exists:pakan,id',
            'resep_pakan_id' => 'required|exists:resep_pakan,id',
            'gudang_id' => 'required|exists:gudang,id',
            'jumlah' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string',
            'biaya_lain' => 'nullable|array',
            'biaya_lain.*.nama_biaya' => 'required_with:biaya_lain|string|max:100',
            'biaya_lain.*.jumlah' => 'required_with:biaya_lain|numeric|min:0',
        ]);

        $resep = ResepPakan::with('details.bahanPakan')
            ->where('id', $validated['resep_pakan_id'])
            ->where('pakan_id', $validated['pakan_id'])
            ->first();

        if (!$resep) {
            return back()->with('error', 'Resep tidak cocok dengan pakan hasil.')->withInput();
        }

        $gudangId = $validated['gudang_id'];
        $jumlah = (float) $validated['jumlah'];
        $details = [];
        $hppBahan = 0;

        foreach ($resep->details as $d) {
            $bahan = $d->bahanPakan;
            if (!$bahan || $bahan->status !== 'aktif') {
                return back()->with('error', "Bahan {$bahan?->nama} tidak aktif.")->withInput();
            }
            if (is_null($bahan->harga) || $bahan->harga <= 0) {
                return back()->with('error', "Bahan {$bahan->nama} belum memiliki harga.")->withInput();
            }

            $butuh = (float) $d->jumlah * $jumlah;
            $stok = BahanPakanStok::where('bahan_pakan_id', $bahan->id)
                ->where('gudang_id', $gudangId)
                ->first();
            $stokTersedia = $stok ? (float) $stok->jumlah : 0;

            if ($stokTersedia < $butuh) {
                return back()->with('error', "Stok {$bahan->nama} tidak cukup. Butuh {$butuh} {$bahan->satuan}, tersedia {$stokTersedia} {$bahan->satuan}.")->withInput();
            }

            $subtotal = $butuh * (float) $bahan->harga;
            $hppBahan += $subtotal;

            $details[] = [
                'bahan_pakan_id' => $bahan->id,
                'jumlah_pakai' => $butuh,
                'harga_satuan' => $bahan->harga,
                'subtotal' => $subtotal,
            ];
        }

        $biayaLainInput = $validated['biaya_lain'] ?? [];
        $biayaLainTotal = 0;
        foreach ($biayaLainInput as $b) {
            $biayaLainTotal += (float) $b['jumlah'];
        }

        $hppTotal = $hppBahan + $biayaLainTotal;
        $hppPerSatuan = $hppTotal / $jumlah;

        try {
            DB::transaction(function () use ($validated, $resep, $jumlah, $gudangId, $hppBahan, $biayaLainTotal, $hppTotal, $hppPerSatuan, $details, $biayaLainInput) {
                // Kurangi stok bahan
                foreach ($details as $d) {
                    $stok = BahanPakanStok::where('bahan_pakan_id', $d['bahan_pakan_id'])
                        ->where('gudang_id', $gudangId)
                        ->first();

                    $jumlahLama = (float) $stok->jumlah;
                    $jumlahBaru = -$d['jumlah_pakai']; // negatif = penggunaan
                    $total = $jumlahLama + $jumlahBaru;

                    $stok->update(['jumlah' => $total, 'tanggal' => $validated['tanggal']]);

                    BahanPakanStokLog::create([
                        'bahan_pakan_stok_id' => $stok->id,
                        'jumlah_lama' => $jumlahLama,
                        'jumlah_baru' => $jumlahBaru,
                        'total' => $total,
                        'tanggal' => $validated['tanggal'],
                        'keterangan' => 'Produksi ' . ($resep->pakan->nama ?? ''),
                        'created_by' => auth()->id(),
                    ]);
                }

                // Simpan produksi
                $produksi = ProduksiPakan::create([
                    'tanggal' => $validated['tanggal'],
                    'pakan_id' => $validated['pakan_id'],
                    'resep_pakan_id' => $validated['resep_pakan_id'],
                    'gudang_id' => $gudangId,
                    'jumlah' => $jumlah,
                    'hpp_bahan' => $hppBahan,
                    'biaya_lain' => $biayaLainTotal,
                    'hpp_total' => $hppTotal,
                    'hpp_per_satuan' => $hppPerSatuan,
                    'keterangan' => $validated['keterangan'] ?? null,
                    'status' => 'selesai',
                    'created_by' => auth()->id(),
                ]);

                foreach ($details as $d) {
                    $produksi->details()->create($d);
                }

                foreach ($biayaLainInput as $b) {
                    $produksi->biayaLain()->create([
                        'nama_biaya' => $b['nama_biaya'],
                        'jumlah' => (float) $b['jumlah'],
                    ]);
                }

                // Tambah stok pakan hasil
                $pakanStok = PakanStok::where('pakan_id', $validated['pakan_id'])
                    ->where('gudang_id', $gudangId)
                    ->first();

                $jumlahLama = $pakanStok ? (float) $pakanStok->jumlah : 0;
                $total = $jumlahLama + $jumlah;

                if (!$pakanStok) {
                    $pakanStok = PakanStok::create([
                        'pakan_id' => $validated['pakan_id'],
                        'gudang_id' => $gudangId,
                        'jumlah' => $total,
                        'tanggal' => $validated['tanggal'],
                        'keterangan' => $validated['keterangan'] ?? null,
                        'created_by' => auth()->id(),
                    ]);
                } else {
                    $pakanStok->update(['jumlah' => $total, 'tanggal' => $validated['tanggal']]);
                }

                PakanStokLog::create([
                    'pakan_stok_id' => $pakanStok->id,
                    'jumlah_lama' => $jumlahLama,
                    'jumlah_baru' => $jumlah,
                    'total' => $total,
                    'harga_satuan' => $hppPerSatuan,
                    'tanggal' => $validated['tanggal'],
                    'keterangan' => 'Produksi pakan',
                    'created_by' => auth()->id(),
                ]);

                // Update harga pokok pakan
                $pakan = Pakan::find($validated['pakan_id']);
                $stokSaatIni = (float) PakanStok::where('pakan_id', $pakan->id)->sum('jumlah');
                $hppLama = (float) ($pakan->harga_pokok ?? 0);

                if ($stokSaatIni <= $jumlah) {
                    $pakan->harga_pokok = $hppPerSatuan;
                } else {
                    $pakan->harga_pokok = (($stokSaatIni - $jumlah) * $hppLama + $jumlah * $hppPerSatuan) / $stokSaatIni;
                }
                $pakan->save();
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Produksi gagal: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('pakan.produksi.index')->with('success', 'Produksi pakan berhasil disimpan.');
    }

    public function show(ProduksiPakan $produksi)
    {
        $produksi->load(['pakan', 'resepPakan', 'gudang', 'details.bahanPakan', 'biayaLain', 'user']);
        return view('pakan.produksi.show', compact('produksi'));
    }

    public function destroy(ProduksiPakan $produksi)
    {
        if ($produksi->status !== 'selesai') {
            return back()->with('error', 'Hanya produksi dengan status selesai yang dapat dibatalkan.');
        }

        $gudangId = $produksi->gudang_id;
        $jumlah = (float) $produksi->jumlah;
        $pakanId = $produksi->pakan_id;
        $hppPerSatuan = (float) $produksi->hpp_per_satuan;

        $pakanStok = PakanStok::where('pakan_id', $pakanId)
            ->where('gudang_id', $gudangId)
            ->first();

        if (!$pakanStok || (float) $pakanStok->jumlah < $jumlah) {
            return back()->with('error', 'Stok pakan hasil tidak cukup untuk membatalkan produksi.');
        }

        try {
            DB::transaction(function () use ($produksi, $gudangId, $pakanStok, $jumlah, $pakanId, $hppPerSatuan) {
                // Kembalikan stok bahan
                foreach ($produksi->details as $d) {
                    $stok = BahanPakanStok::where('bahan_pakan_id', $d->bahan_pakan_id)
                        ->where('gudang_id', $gudangId)
                        ->first();

                    if ($stok) {
                        $jumlahLama = (float) $stok->jumlah;
                        $jumlahBaru = (float) $d->jumlah_pakai;
                        $total = $jumlahLama + $jumlahBaru;

                        $stok->update(['jumlah' => $total]);

                        BahanPakanStokLog::create([
                            'bahan_pakan_stok_id' => $stok->id,
                            'jumlah_lama' => $jumlahLama,
                            'jumlah_baru' => $jumlahBaru,
                            'total' => $total,
                            'tanggal' => now(),
                            'keterangan' => 'Pembatalan produksi ' . ($produksi->pakan->nama ?? ''),
                            'created_by' => auth()->id(),
                        ]);
                    } else {
                        $stok = BahanPakanStok::create([
                            'bahan_pakan_id' => $d->bahan_pakan_id,
                            'gudang_id' => $gudangId,
                            'jumlah' => $jumlahBaru,
                            'tanggal' => now(),
                            'keterangan' => 'Pembatalan produksi',
                            'created_by' => auth()->id(),
                        ]);

                        BahanPakanStokLog::create([
                            'bahan_pakan_stok_id' => $stok->id,
                            'jumlah_lama' => 0,
                            'jumlah_baru' => $jumlahBaru,
                            'total' => $jumlahBaru,
                            'tanggal' => now(),
                            'keterangan' => 'Pembatalan produksi',
                            'created_by' => auth()->id(),
                        ]);
                    }
                }

                // Kurangi stok pakan hasil
                $jumlahLama = (float) $pakanStok->jumlah;
                $total = $jumlahLama - $jumlah;

                $pakanStok->update(['jumlah' => $total]);

                PakanStokLog::create([
                    'pakan_stok_id' => $pakanStok->id,
                    'jumlah_lama' => $jumlahLama,
                    'jumlah_baru' => -$jumlah,
                    'total' => $total,
                    'tanggal' => now(),
                    'keterangan' => 'Pembatalan produksi',
                    'created_by' => auth()->id(),
                ]);

                // Recalc harga pokok
                $pakan = Pakan::find($pakanId);
                $stokSaatIni = (float) PakanStok::where('pakan_id', $pakanId)->sum('jumlah');
                $hppLama = (float) ($pakan->harga_pokok ?? 0);

                if ($stokSaatIni <= 0) {
                    $pakan->harga_pokok = null;
                } else {
                    $totalNilai = ($stokSaatIni + $jumlah) * $hppLama - ($jumlah * $hppPerSatuan);
                    $pakan->harga_pokok = max(0, $totalNilai / $stokSaatIni);
                }
                $pakan->save();

                // Hapus produksi
                $produksi->details()->delete();
                $produksi->biayaLain()->delete();
                $produksi->delete();
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Pembatalan produksi gagal: ' . $e->getMessage());
        }

        return redirect()->route('pakan.produksi.index')->with('success', 'Produksi pakan berhasil dibatalkan.');
    }
}
