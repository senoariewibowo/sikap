<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `transaksi_eceran` ADD `satuan` ENUM('per_butir','per_kg') NOT NULL DEFAULT 'per_butir' AFTER `total_butir`");
        DB::statement("ALTER TABLE `transaksi_eceran` ADD `berat_kg` DECIMAL(8,2) NULL AFTER `satuan`");
        DB::statement("UPDATE `transaksi_eceran` SET `satuan` = 'per_butir'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `transaksi_eceran` DROP COLUMN `berat_kg`");
        DB::statement("ALTER TABLE `transaksi_eceran` DROP COLUMN `satuan`");
    }
};
