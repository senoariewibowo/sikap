<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_telur', function (Blueprint $table) {
            $table->dropColumn('foto');
        });

        Schema::create('produksi_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_telur_id')->constrained('produksi_telur')->onDelete('cascade');
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_foto');
        Schema::table('produksi_telur', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('sisa');
        });
    }
};
