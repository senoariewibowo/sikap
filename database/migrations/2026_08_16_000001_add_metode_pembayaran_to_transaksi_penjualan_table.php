<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_penjualan', function (Blueprint $table) {
            $table->enum('metode_pembayaran', ['tunai', 'transfer'])->default('tunai')->after('status_pembayaran');
            $table->text('catatan_pembayaran')->nullable()->after('metode_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_penjualan', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'catatan_pembayaran']);
        });
    }
};
