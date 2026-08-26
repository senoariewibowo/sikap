<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('stok_telur_keluar')->whereIn('id', function ($q) {
            $q->select('id')->from('stok_telur_eceran');
        })->delete();

        DB::statement("ALTER TABLE `stok_telur_keluar` DROP COLUMN `tipe`");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `stok_telur_keluar` ADD `tipe` ENUM('customer','eceran') NOT NULL DEFAULT 'customer' AFTER `tanggal`");
    }
};
