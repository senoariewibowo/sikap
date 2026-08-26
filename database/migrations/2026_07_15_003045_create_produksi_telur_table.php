<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi_telur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandang_id')->constrained('kandang')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah_butir');
            $table->decimal('berat_kg', 8, 2);
            $table->enum('grade', ['besar', 'sedang', 'kecil', 'retak']);
            $table->string('shift', 20)->nullable();
            $table->foreignId('input_by')->constrained('users');
            $table->timestamps();

            $table->index(['kandang_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_telur');
    }
};
