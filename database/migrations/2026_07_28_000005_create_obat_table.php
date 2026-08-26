<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->nullable()->unique();
            $table->string('nama', 100);
            $table->enum('jenis', ['obat', 'vitamin'])->default('obat');
            $table->string('satuan', 20)->default('ml');
            $table->decimal('stok_minimal', 10, 2)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};
