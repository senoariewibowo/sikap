<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_pakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pakan_id')->constrained('pakan')->cascadeOnDelete();
            $table->string('nama_resep', 100);
            $table->boolean('is_default')->default(false);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_pakan');
    }
};
