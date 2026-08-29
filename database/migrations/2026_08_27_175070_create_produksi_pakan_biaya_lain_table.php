<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi_pakan_biaya_lain', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_pakan_id')->constrained('produksi_pakan')->cascadeOnDelete();
            $table->string('nama_biaya', 100);
            $table->decimal('jumlah', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_pakan_biaya_lain');
    }
};
