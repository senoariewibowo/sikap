<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_eceran', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('stok_telur_eceran_id')->nullable()->constrained('stok_telur_eceran')->nullOnDelete();
            $table->integer('jumlah_butir');
            $table->decimal('harga_per_butir', 10, 0);
            $table->decimal('total_harga', 12, 0);
            $table->text('keterangan')->nullable();
            $table->foreignId('input_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_eceran');
    }
};
