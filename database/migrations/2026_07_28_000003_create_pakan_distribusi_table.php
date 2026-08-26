<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pakan_distribusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pakan_id')->constrained('pakan')->cascadeOnDelete();
            $table->foreignId('gudang_id')->constrained('gudang')->cascadeOnDelete();
            $table->foreignId('kandang_id')->constrained('kandang')->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal_kirim');
            $table->enum('status', ['dikirim', 'diterima'])->default('dikirim');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('diterima_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_diterima')->nullable();
            $table->timestamps();

            $table->index(['pakan_id', 'gudang_id', 'status']);
            $table->index(['kandang_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pakan_distribusi');
    }
};
