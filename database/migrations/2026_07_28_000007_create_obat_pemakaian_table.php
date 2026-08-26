<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obat_pemakaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obat')->cascadeOnDelete();
            $table->foreignId('kandang_id')->constrained('kandang')->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['kandang_id', 'obat_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat_pemakaian');
    }
};
