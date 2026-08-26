<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `transaksi_eceran` ADD `total_butir` INT NOT NULL DEFAULT 0 AFTER `tanggal`");
        DB::statement("UPDATE `transaksi_eceran` SET `total_butir` = `jumlah_butir`");

        Schema::create('transaksi_eceran_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_eceran_id')->constrained('transaksi_eceran')->onDelete('cascade');
            $table->foreignId('stok_telur_eceran_id')->nullable()->constrained('stok_telur_eceran')->nullOnDelete();
            $table->integer('jumlah_butir');
            $table->timestamps();
        });

        DB::statement("INSERT INTO `transaksi_eceran_detail` (`transaksi_eceran_id`, `stok_telur_eceran_id`, `jumlah_butir`, `created_at`, `updated_at`) SELECT `id`, `stok_telur_eceran_id`, `jumlah_butir`, `created_at`, `updated_at` FROM `transaksi_eceran`");

        DB::statement("ALTER TABLE `transaksi_eceran` DROP FOREIGN KEY `transaksi_eceran_stok_telur_eceran_id_foreign`");
        Schema::table('transaksi_eceran', function (Blueprint $table) {
            $table->dropColumn('stok_telur_eceran_id');
            $table->dropColumn('jumlah_butir');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_eceran', function (Blueprint $table) {
            $table->foreignId('stok_telur_eceran_id')->nullable()->after('tanggal')->constrained('stok_telur_eceran')->nullOnDelete();
            $table->integer('jumlah_butir')->default(0)->after('stok_telur_eceran_id');
        });

        DB::statement("UPDATE `transaksi_eceran` t SET `stok_telur_eceran_id` = (SELECT `stok_telur_eceran_id` FROM `transaksi_eceran_detail` WHERE `transaksi_eceran_id` = t.id LIMIT 1), `jumlah_butir` = `total_butir`");

        Schema::dropIfExists('transaksi_eceran_detail');
        Schema::table('transaksi_eceran', function (Blueprint $table) {
            $table->dropColumn('total_butir');
        });
    }
};
