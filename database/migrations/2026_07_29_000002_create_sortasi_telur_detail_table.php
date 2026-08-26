<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sortasi_telur_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sortasi_telur_id')->constrained('sortasi_telur')->onDelete('cascade');
            $table->integer('butir');
            $table->integer('karpet');
            $table->decimal('berat', 8, 2)->default(15.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sortasi_telur_detail');
    }
};
