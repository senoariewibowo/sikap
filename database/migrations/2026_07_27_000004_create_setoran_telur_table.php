<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran_telur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_telur_id')->constrained('produksi_telur')->cascadeOnDelete();
            $table->foreignId('gudang_id')->constrained('gudang')->cascadeOnDelete();
            $table->foreignId('kandang_id')->constrained('kandang')->cascadeOnDelete();
            $table->date('tanggal_setor');
            $table->integer('jumlah_diterima');
            $table->integer('selisih')->default(0);
            $table->text('catatan')->nullable();
            $table->foreignId('input_by')->constrained('users');
            $table->timestamps();

            $table->index(['gudang_id', 'tanggal_setor']);
            $table->index(['kandang_id', 'tanggal_setor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_telur');
    }
};
