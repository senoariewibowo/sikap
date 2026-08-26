<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `sortasi_telur` DROP FOREIGN KEY `sortasi_telur_gudang_id_foreign`");
        DB::statement("ALTER TABLE `sortasi_telur` DROP INDEX `sortasi_telur_gudang_id_tanggal_shift_unique`");
        DB::statement("ALTER TABLE `sortasi_telur` ADD INDEX `sortasi_telur_gudang_id_index` (`gudang_id`)");
        DB::statement("ALTER TABLE `sortasi_telur` ADD UNIQUE `sortasi_telur_gudang_kandang_tanggal_shift_unique` (`gudang_id`, `kandang_id`, `tanggal`, `shift`)");
        DB::statement("ALTER TABLE `sortasi_telur` ADD CONSTRAINT `sortasi_telur_gudang_id_foreign` FOREIGN KEY (`gudang_id`) REFERENCES `gudang` (`id`) ON DELETE CASCADE");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `sortasi_telur` DROP FOREIGN KEY `sortasi_telur_gudang_id_foreign`");
        DB::statement("ALTER TABLE `sortasi_telur` DROP INDEX `sortasi_telur_gudang_kandang_tanggal_shift_unique`");
        DB::statement("ALTER TABLE `sortasi_telur` DROP INDEX `sortasi_telur_gudang_id_index`");
        DB::statement("ALTER TABLE `sortasi_telur` ADD UNIQUE `sortasi_telur_gudang_id_tanggal_shift_unique` (`gudang_id`, `tanggal`, `shift`)");
        DB::statement("ALTER TABLE `sortasi_telur` ADD CONSTRAINT `sortasi_telur_gudang_id_foreign` FOREIGN KEY (`gudang_id`) REFERENCES `gudang` (`id`) ON DELETE CASCADE");
    }
};
