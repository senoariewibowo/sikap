<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemakaian_pakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_pakan_id')->constrained('jenis_pakan')->cascadeOnDelete();
            $table->foreignId('kandang_id')->constrained('kandang')->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index(['kandang_id', 'jenis_pakan_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemakaian_pakan');
    }
};
