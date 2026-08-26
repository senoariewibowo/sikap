<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `stok_telur_keluar` ADD `tipe` ENUM('customer','eceran') NOT NULL DEFAULT 'customer' AFTER `tanggal`");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `stok_telur_keluar` DROP COLUMN `tipe`");
    }
};
