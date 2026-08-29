<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi_pakan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_pakan_id')->constrained('produksi_pakan')->cascadeOnDelete();
            $table->foreignId('bahan_pakan_id')->constrained('bahan_pakan')->cascadeOnDelete();
            $table->decimal('jumlah_pakai', 12, 2);
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('subtotal', 14, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_pakan_detail');
    }
};
