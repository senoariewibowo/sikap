<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_penjualan_id')->constrained('transaksi_penjualan')->cascadeOnDelete();
            $table->foreignId('stok_telur_keluar_id')->constrained('stok_telur_keluar')->cascadeOnDelete();
            $table->integer('jumlah_butir');
            $table->decimal('berat_kg', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_stok');
    }
};
