<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->integer('karpet')->nullable()->after('berat_kg');
            $table->integer('peti')->nullable()->after('karpet');
        });
    }

    public function down(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->dropColumn(['karpet', 'peti']);
        });
    }
};
