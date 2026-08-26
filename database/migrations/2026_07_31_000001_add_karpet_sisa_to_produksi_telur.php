<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `produksi_telur` ADD `karpet` INT NOT NULL DEFAULT 0 AFTER `jumlah_butir`");
        DB::statement("ALTER TABLE `produksi_telur` ADD `sisa` INT NOT NULL DEFAULT 0 AFTER `karpet`");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `produksi_telur` DROP COLUMN `karpet`");
        DB::statement("ALTER TABLE `produksi_telur` DROP COLUMN `sisa`");
    }
};
