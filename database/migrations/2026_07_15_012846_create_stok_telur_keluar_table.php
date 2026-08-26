<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_telur_keluar', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('kandang_id')->nullable()->constrained('kandang')->nullOnDelete();
            $table->integer('jumlah_butir');
            $table->decimal('berat_kg', 8, 2);
            $table->enum('grade', ['besar', 'sedang', 'kecil', 'retak']);
            $table->string('tujuan', 200)->nullable();
            $table->string('no_referensi', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('input_by')->constrained('users');
            $table->timestamps();
            $table->index(['tanggal', 'kandang_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_telur_keluar');
    }
};
