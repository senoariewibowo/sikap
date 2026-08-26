<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JenisPakan;
use App\Models\StokPakan;
use App\Models\PemakaianPakan;
use App\Models\Obat;
use App\Models\ObatStok;
use App\Models\ObatPemakaian;
use App\Models\Kandang;

class MigrateObatData extends Command
{
    protected $signature = 'obat:migrate-data';
    protected $description = 'Migrasi data obat/vitamin dari tabel lama (jenis_pakan, stok_pakan, pemakaian_pakan) ke tabel baru';

    public function handle(): int
    {
        $this->info('Memulai migrasi data obat & vitamin...');

        $this->migrateMaster();
        $this->migrateStok();
        $this->migratePemakaian();

        $this->info('Migrasi data obat & vitamin selesai.');
        return 0;
    }

    private function migrateMaster(): void
    {
        $count = 0;
        $oldItems = JenisPakan::whereIn('kategori', ['obat', 'vitamin'])->get();

        foreach ($oldItems as $old) {
            $existing = Obat::where('nama', $old->nama)->where('jenis', $old->kategori)->first();
            if ($existing) {
                $this->line("  Skip: {$old->nama} (sudah ada)");
                continue;
            }

            Obat::create([
                'kode' => null,
                'nama' => $old->nama,
                'jenis' => $old->kategori,
                'satuan' => $old->satuan ?? 'ml',
                'stok_minimal' => $old->stok_minimal,
                'status' => 'aktif',
            ]);
            $count++;
        }

        $this->info("  Master obat/vitamin: {$count} data dimigrasi");
    }

    private function migrateStok(): void
    {
        $count = 0;
        $oldStoks = StokPakan::with(['jenisPakan', 'kandang'])
            ->whereHas('jenisPakan', fn($q) => $q->whereIn('kategori', ['obat', 'vitamin']))
            ->where('tipe', 'masuk')
            ->get();

        foreach ($oldStoks as $old) {
            $obat = Obat::where('nama', $old->jenisPakan->nama ?? '')
                ->where('jenis', $old->jenisPakan->kategori ?? 'obat')
                ->first();
            if (!$obat) continue;

            $gudangId = null;
            if ($old->kandang_id && $old->kandang) {
                $gudangId = $old->kandang->gudang_id;
            }

            if (!$gudangId) {
                $this->line("  Skip stok: {$old->jenisPakan->nama} (tidak ada gudang)");
                continue;
            }

            ObatStok::create([
                'obat_id' => $obat->id,
                'gudang_id' => $gudangId,
                'jumlah' => $old->jumlah_kg,
                'tanggal' => $old->tanggal,
                'keterangan' => $old->keterangan,
                'created_by' => $old->created_by,
            ]);
            $count++;
        }

        $this->info("  Stok obat: {$count} data dimigrasi");
    }

    private function migratePemakaian(): void
    {
        $count = 0;
        $oldPemakaians = PemakaianPakan::with(['jenisPakan'])
            ->whereHas('jenisPakan', fn($q) => $q->whereIn('kategori', ['obat', 'vitamin']))
            ->get();

        foreach ($oldPemakaians as $old) {
            $obat = Obat::where('nama', $old->jenisPakan->nama ?? '')
                ->where('jenis', $old->jenisPakan->kategori ?? 'obat')
                ->first();
            if (!$obat) continue;

            ObatPemakaian::create([
                'obat_id' => $obat->id,
                'kandang_id' => $old->kandang_id,
                'jumlah' => $old->jumlah,
                'tanggal' => $old->tanggal,
                'keterangan' => $old->keterangan,
                'created_by' => $old->created_by,
            ]);
            $count++;
        }

        $oldStokKeluars = StokPakan::with(['jenisPakan'])
            ->whereHas('jenisPakan', fn($q) => $q->whereIn('kategori', ['obat', 'vitamin']))
            ->where('tipe', 'keluar')
            ->get();

        foreach ($oldStokKeluars as $old) {
            $obat = Obat::where('nama', $old->jenisPakan->nama ?? '')
                ->where('jenis', $old->jenisPakan->kategori ?? 'obat')
                ->first();
            if (!$obat || !$old->kandang_id) continue;

            ObatPemakaian::create([
                'obat_id' => $obat->id,
                'kandang_id' => $old->kandang_id,
                'jumlah' => $old->jumlah_kg,
                'tanggal' => $old->tanggal,
                'keterangan' => $old->keterangan,
                'created_by' => $old->created_by,
            ]);
            $count++;
        }

        $this->info("  Pemakaian obat: {$count} data dimigrasi");
    }
}
