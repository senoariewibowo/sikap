<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_penjualan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('customer_id')->constrained('customer')->cascadeOnDelete();
            $table->foreignId('stok_telur_keluar_id')->constrained('stok_telur_keluar')->cascadeOnDelete();
            $table->enum('grade', ['besar', 'sedang', 'kecil', 'retak']);
            $table->integer('jumlah_butir');
            $table->decimal('berat_kg', 8, 2);
            $table->decimal('harga_per_satuan', 10, 2);
            $table->decimal('total_harga', 12, 2);
            $table->enum('status_pembayaran', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->string('no_invoice', 50)->nullable();
            $table->foreignId('input_by')->constrained('users');
            $table->timestamps();
            $table->index('tanggal');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_penjualan');
    }
};
