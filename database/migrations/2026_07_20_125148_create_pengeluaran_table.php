<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('kategori_pengeluaran_id')->constrained('kategori_pengeluaran')->cascadeOnDelete();
            $table->foreignId('kandang_id')->nullable()->constrained('kandang')->nullOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->text('keterangan')->nullable();
            $table->string('bukti')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index(['tanggal', 'kategori_pengeluaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
