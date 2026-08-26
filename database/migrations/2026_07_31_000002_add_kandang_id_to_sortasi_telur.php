<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `sortasi_telur` ADD `kandang_id` BIGINT UNSIGNED NULL AFTER `gudang_id`");
        DB::statement("ALTER TABLE `sortasi_telur` ADD CONSTRAINT `fk_sortasi_kandang` FOREIGN KEY (`kandang_id`) REFERENCES `kandang`(`id`) ON DELETE CASCADE");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `sortasi_telur` DROP FOREIGN KEY `fk_sortasi_kandang`");
        DB::statement("ALTER TABLE `sortasi_telur` DROP COLUMN `kandang_id`");
    }
};
