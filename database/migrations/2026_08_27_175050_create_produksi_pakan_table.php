<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi_pakan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->nullable()->unique();
            $table->date('tanggal');
            $table->foreignId('pakan_id')->constrained('pakan')->cascadeOnDelete();
            $table->foreignId('resep_pakan_id')->constrained('resep_pakan')->cascadeOnDelete();
            $table->foreignId('gudang_id')->constrained('gudang')->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->decimal('hpp_bahan', 14, 2)->default(0);
            $table->decimal('biaya_lain', 14, 2)->default(0);
            $table->decimal('hpp_total', 14, 2)->default(0);
            $table->decimal('hpp_per_satuan', 14, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'selesai', 'batal'])->default('selesai');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_pakan');
    }
};
