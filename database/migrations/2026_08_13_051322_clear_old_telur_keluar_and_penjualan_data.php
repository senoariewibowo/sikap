<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus semua data lama telur keluar, penjualan, dan detail penjualan
        // untuk memulai sistem baru dari nol.
        // Transaksi penjualan ikut terhapus karena FK cascade ke stok_telur_keluar.
        DB::table('penjualan_stok')->delete();
        DB::table('transaksi_penjualan')->delete();
        DB::table('stok_telur_keluar')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data yang sudah dihapus tidak bisa dikembalikan.
    }
};
