<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kasir_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kasir_transaction_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name', 150);
            $table->float('harga');
            $table->integer('qty');
            $table->float('subtotal');
            $table->timestamps();

            $table->foreign('kasir_transaction_id')->references('id')->on('kasir_transactions')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kasir_transaction_details');
    }
};
