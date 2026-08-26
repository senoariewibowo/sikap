<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('populasi_ayam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->constrained('kandang')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah_masuk')->default(0);
            $table->integer('jumlah_mati')->default(0);
            $table->integer('jumlah_afkir')->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['kandang_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('populasi_ayam');
    }
};
