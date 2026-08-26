<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_eceran', function (Blueprint $table) {
            $table->integer('karpet')->nullable()->after('berat_kg');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_eceran', function (Blueprint $table) {
            $table->dropColumn('karpet');
        });
    }
};
