<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_pakan_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_pakan_id')->constrained('bahan_pakan')->cascadeOnDelete();
            $table->foreignId('gudang_id')->constrained('gudang')->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['bahan_pakan_id', 'gudang_id']);
            $table->index(['bahan_pakan_id', 'gudang_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_pakan_stok');
    }
};
