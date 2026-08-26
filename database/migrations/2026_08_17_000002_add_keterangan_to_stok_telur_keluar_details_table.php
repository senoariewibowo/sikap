<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_telur_keluar_details', function (Blueprint $table) {
            $table->string('keterangan', 255)->nullable()->after('peti');
        });
    }

    public function down(): void
    {
        Schema::table('stok_telur_keluar_details', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
