<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_pakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->nullable()->constrained('kandang')->nullOnDelete();
            $table->foreignId('jenis_pakan_id')->constrained('jenis_pakan')->cascadeOnDelete();
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->decimal('jumlah_kg', 10, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['jenis_pakan_id', 'tanggal']);
            $table->index(['kandang_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_pakan');
    }
};
