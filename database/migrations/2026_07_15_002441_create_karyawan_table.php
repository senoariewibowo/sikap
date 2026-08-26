<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 30)->unique();
            $table->string('nama', 100);
            $table->string('no_hp', 20)->nullable();
            $table->string('alamat', 200)->nullable();
            $table->string('jabatan', 50);
            $table->date('tanggal_masuk');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('foto')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
