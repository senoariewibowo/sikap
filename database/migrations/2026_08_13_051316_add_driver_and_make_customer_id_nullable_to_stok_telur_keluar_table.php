<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->string('driver')->nullable()->after('customer_id');

            $table->dropForeign(['customer_id']);
            $table->foreignId('customer_id')->nullable()->change();
            $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->dropColumn('driver');

            $table->dropForeign(['customer_id']);
            $table->foreignId('customer_id')->change();
            $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');
        });
    }
};
