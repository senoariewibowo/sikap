<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->text('ttd_pengirim_img')->nullable()->after('ttd_mengetahui_at');
            $table->text('ttd_mengetahui_img')->nullable()->after('ttd_pengirim_img');
        });
        Schema::table('transaksi_penjualan', function (Blueprint $table) {
            $table->text('ttd_petugas_img')->nullable()->after('ttd_petugas_at');
        });
    }

    public function down(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->dropColumn(['ttd_pengirim_img', 'ttd_mengetahui_img']);
        });
        Schema::table('transaksi_penjualan', function (Blueprint $table) {
            $table->dropColumn('ttd_petugas_img');
        });
    }
};
