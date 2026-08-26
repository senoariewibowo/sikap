<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kandang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kandang', 20)->unique();
            $table->string('nama_kandang', 100);
            $table->string('alamat_jalan', 200);
            $table->string('desa_kelurahan', 100);
            $table->string('kecamatan', 100);
            $table->string('kabupaten_kota', 100);
            $table->string('provinsi', 100);
            $table->string('kode_pos', 10);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('kapasitas');
            $table->enum('tipe_kandang', ['baterai', 'postal', 'closed_house']);
            $table->enum('status', ['aktif', 'renovasi', 'nonaktif'])->default('aktif');
            $table->string('foto')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kandang');
    }
};
