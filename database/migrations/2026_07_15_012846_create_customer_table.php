<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('nama_customer', 150);
            $table->enum('tipe_customer', ['agen', 'pengepul', 'retail', 'korporat'])->default('retail');
            $table->string('alamat', 200)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('kontak_person', 100)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
