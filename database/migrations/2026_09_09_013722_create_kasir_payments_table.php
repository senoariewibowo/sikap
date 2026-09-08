<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kasir_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasir_transaction_id')->constrained('kasir_transactions')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('metode_pembayaran', ['tunai', 'transfer'])->default('tunai');
            $table->decimal('jumlah', 15, 2);
            $table->string('keterangan', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kasir_payments');
    }
};
