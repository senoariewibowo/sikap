<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kasir_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('no_struk', 50)->unique();
            $table->date('tanggal');
            $table->float('total');
            $table->float('bayar');
            $table->float('kembalian');
            $table->unsignedBigInteger('input_by')->nullable();
            $table->timestamps();

            $table->foreign('input_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kasir_transactions');
    }
};
