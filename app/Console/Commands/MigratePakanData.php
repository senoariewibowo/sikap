<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JenisPakan;
use App\Models\StokPakan;
use App\Models\PemakaianPakan;
use App\Models\Pakan;
use App\Models\PakanStok;
use App\Models\PakanDistribusi;
use App\Models\PakanPemakaian;
use App\Models\Kandang;

class MigratePakanData extends Command
{
    protected $signature = 'pakan:migrate-data';
    protected $description = 'Migrasi data pakan dari tabel lama (jenis_pakan, stok_pakan, pemakaian_pakan) ke tabel baru';

    public function handle(): int
    {
        $this->info('Memulai migrasi data pakan...');

        $this->migrateMaster();
        $this->migrateStok();
        $this->migratePemakaian();

        $this->info('Migrasi data pakan selesai.');
        return 0;
    }

    private function migrateMaster(): void
    {
        $count = 0;
        $oldItems = JenisPakan::where('kategori', 'pakan')->get();

        foreach ($oldItems as $old) {
            $existing = Pakan::where('nama', $old->nama)->first();
            if ($existing) {
                $this->line("  Skip: {$old->nama} (sudah ada)");
                continue;
            }

            Pakan::create([
                'kode' => null,
                'nama' => $old->nama,
                'satuan' => $old->satuan ?? 'kg',
                'harga' => $old->harga,
                'stok_minimal' => $old->stok_minimal,
                'status' => 'aktif',
            ]);
            $count++;
        }

        $this->info("  Master pakan: {$count} data dimigrasi");
    }

    private function migrateStok(): void
    {
        $count = 0;
        $oldStoks = StokPakan::with(['jenisPakan', 'kandang'])
            ->whereHas('jenisPakan', fn($q) => $q->where('kategori', 'pakan'))
            ->where('tipe', 'masuk')
            ->get();

        foreach ($oldStoks as $old) {
            $pakan = Pakan::where('nama', $old->jenisPakan->nama ?? '')->first();
            if (!$pakan) continue;

            $gudangId = null;
            if ($old->kandang_id && $old->kandang) {
                $gudangId = $old->kandang->gudang_id;
            }

            if (!$gudangId) {
                $this->line("  Skip stok: {$old->jenisPakan->nama} (tidak ada gudang)");
                continue;
            }

            PakanStok::create([
                'pakan_id' => $pakan->id,
                'gudang_id' => $gudangId,
                'jumlah' => $old->jumlah_kg,
                'tanggal' => $old->tanggal,
                'keterangan' => $old->keterangan,
                'created_by' => $old->created_by,
            ]);
            $count++;
        }

        $this->info("  Stok pakan: {$count} data dimigrasi");
    }

    private function migratePemakaian(): void
    {
        $count = 0;
        $oldPemakaians = PemakaianPakan::with(['jenisPakan', 'kandang'])
            ->whereHas('jenisPakan', fn($q) => $q->where('kategori', 'pakan'))
            ->get();

        foreach ($oldPemakaians as $old) {
            $pakan = Pakan::where('nama', $old->jenisPakan->nama ?? '')->first();
            if (!$pakan) continue;

            PakanPemakaian::create([
                'pakan_id' => $pakan->id,
                'kandang_id' => $old->kandang_id,
                'jumlah' => $old->jumlah,
                'tanggal' => $old->tanggal,
                'keterangan' => $old->keterangan,
                'created_by' => $old->created_by,
            ]);
            $count++;
        }

        $oldStokKeluars = StokPakan::with(['jenisPakan', 'kandang'])
            ->whereHas('jenisPakan', fn($q) => $q->where('kategori', 'pakan'))
            ->where('tipe', 'keluar')
            ->get();

        foreach ($oldStokKeluars as $old) {
            $pakan = Pakan::where('nama', $old->jenisPakan->nama ?? '')->first();
            if (!$pakan || !$old->kandang_id) continue;

            PakanPemakaian::create([
                'pakan_id' => $pakan->id,
                'kandang_id' => $old->kandang_id,
                'jumlah' => $old->jumlah_kg,
                'tanggal' => $old->tanggal,
                'keterangan' => $old->keterangan,
                'created_by' => $old->created_by,
            ]);
            $count++;
        }

        $this->info("  Pemakaian pakan: {$count} data dimigrasi");
    }
}
