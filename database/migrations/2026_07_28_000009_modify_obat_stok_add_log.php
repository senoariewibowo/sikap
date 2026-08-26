<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obat_stok', function (Blueprint $table) {
            $table->unique(['obat_id', 'gudang_id']);
        });

        Schema::create('obat_stok_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_stok_id')->constrained('obat_stok')->cascadeOnDelete();
            $table->decimal('jumlah_lama', 12, 2);
            $table->decimal('jumlah_baru', 12, 2);
            $table->decimal('total', 12, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['obat_stok_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat_stok_log');
        Schema::table('obat_stok', function (Blueprint $table) {
            $table->dropUnique(['obat_id', 'gudang_id']);
        });
    }
};
