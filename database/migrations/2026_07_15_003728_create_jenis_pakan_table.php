<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pakan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kategori', 20)->default('pakan');
            $table->string('satuan', 20)->default('kg');
            $table->decimal('stok_minimal', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pakan');
    }
};
