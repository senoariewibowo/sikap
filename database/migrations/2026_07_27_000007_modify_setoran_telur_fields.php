<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `setoran_telur` CHANGE `jumlah_diterima` `butir` INT NOT NULL");
        DB::statement("ALTER TABLE `setoran_telur` ADD `karpet` INT NOT NULL DEFAULT 0 AFTER `tanggal_setor`");
        DB::statement("ALTER TABLE `setoran_telur` ADD `peti` INT NOT NULL DEFAULT 0 AFTER `karpet`");
        DB::statement("ALTER TABLE `setoran_telur` ADD `berat` DECIMAL(8,2) NULL AFTER `peti`");
        DB::statement("ALTER TABLE `setoran_telur` ADD `pecah` INT NOT NULL DEFAULT 0 AFTER `butir`");
        DB::statement("ALTER TABLE `setoran_telur` ADD `retak` INT NOT NULL DEFAULT 0 AFTER `pecah`");
        DB::statement("ALTER TABLE `setoran_telur` ADD `kopong` INT NOT NULL DEFAULT 0 AFTER `retak`");
        DB::statement("ALTER TABLE `setoran_telur` ADD `sisa` INT NOT NULL DEFAULT 0 AFTER `kopong`");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `setoran_telur` DROP COLUMN `karpet`");
        DB::statement("ALTER TABLE `setoran_telur` DROP COLUMN `peti`");
        DB::statement("ALTER TABLE `setoran_telur` DROP COLUMN `berat`");
        DB::statement("ALTER TABLE `setoran_telur` DROP COLUMN `pecah`");
        DB::statement("ALTER TABLE `setoran_telur` DROP COLUMN `retak`");
        DB::statement("ALTER TABLE `setoran_telur` DROP COLUMN `kopong`");
        DB::statement("ALTER TABLE `setoran_telur` DROP COLUMN `sisa`");
        DB::statement("ALTER TABLE `setoran_telur` CHANGE `butir` `jumlah_diterima` INT NOT NULL");
    }
};
