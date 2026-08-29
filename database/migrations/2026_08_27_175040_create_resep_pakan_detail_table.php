<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_pakan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_pakan_id')->constrained('resep_pakan')->cascadeOnDelete();
            $table->foreignId('bahan_pakan_id')->constrained('bahan_pakan')->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_pakan_detail');
    }
};
