<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harga_telur', function (Blueprint $table) {
            $table->id();
            $table->enum('grade', ['besar', 'sedang', 'kecil', 'retak']);
            $table->decimal('harga', 10, 2);
            $table->enum('satuan', ['per_butir', 'per_kg'])->default('per_butir');
            $table->foreignId('customer_id')->nullable()->constrained('customer')->nullOnDelete();
            $table->date('tanggal_mulai_berlaku');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index(['grade', 'tanggal_mulai_berlaku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harga_telur');
    }
};
