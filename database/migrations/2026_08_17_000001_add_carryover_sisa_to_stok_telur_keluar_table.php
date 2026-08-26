<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->boolean('carryover_sisa')->default(false)->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->dropColumn('carryover_sisa');
        });
    }
};