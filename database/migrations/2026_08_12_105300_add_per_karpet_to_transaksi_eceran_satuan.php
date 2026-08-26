<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `transaksi_eceran` MODIFY COLUMN `satuan` ENUM('per_butir','per_kg','per_karpet') NOT NULL DEFAULT 'per_butir'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `transaksi_eceran` MODIFY COLUMN `satuan` ENUM('per_butir','per_kg') NOT NULL DEFAULT 'per_butir'");
    }
};
