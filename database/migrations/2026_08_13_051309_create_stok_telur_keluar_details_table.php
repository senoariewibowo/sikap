<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stok_telur_keluar_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_telur_keluar_id')->constrained('stok_telur_keluar')->cascadeOnDelete();
            $table->foreignId('sortasi_telur_detail_id')->constrained('sortasi_telur_detail')->restrictOnDelete();
            $table->integer('jumlah_butir');
            $table->decimal('berat_kg', 8, 2);
            $table->integer('karpet')->default(0);
            $table->integer('peti')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_telur_keluar_details');
    }
};
