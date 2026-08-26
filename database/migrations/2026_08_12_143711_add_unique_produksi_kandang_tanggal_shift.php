<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus duplikat: simpan hanya 1 record per (kandang_id, tanggal, shift)
        DB::statement("
            DELETE p1 FROM produksi_telur p1
            INNER JOIN produksi_telur p2
            WHERE p1.id < p2.id
              AND p1.kandang_id = p2.kandang_id
              AND p1.tanggal = p2.tanggal
              AND p1.shift = p2.shift
        ");

        DB::statement("ALTER TABLE `produksi_telur` ADD UNIQUE `produksi_telur_kandang_tanggal_shift_unique` (`kandang_id`, `tanggal`, `shift`)");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `produksi_telur` DROP INDEX `produksi_telur_kandang_tanggal_shift_unique`");
    }
};
