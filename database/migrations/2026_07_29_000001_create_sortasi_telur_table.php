<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sortasi_telur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gudang_id')->constrained('gudang')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('shift', 20);
            $table->integer('pecah')->default(0);
            $table->integer('retak')->default(0);
            $table->integer('kopong')->default(0);
            $table->integer('sisa')->default(0);
            $table->text('catatan')->nullable();
            $table->foreignId('input_by')->constrained('users');
            $table->timestamps();

            $table->unique(['gudang_id', 'tanggal', 'shift']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sortasi_telur');
    }
};
