<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kasir_transaction_details', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->change();
            $table->decimal('subtotal', 15, 2)->change();
        });

        Schema::table('product_prices', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('modal', 15, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kasir_transaction_details', function (Blueprint $table) {
            $table->double('harga')->change();
            $table->double('subtotal')->change();
        });

        Schema::table('product_prices', function (Blueprint $table) {
            $table->double('harga')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->double('modal')->nullable()->change();
        });
    }
};
