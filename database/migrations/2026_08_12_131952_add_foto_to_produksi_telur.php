<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_telur', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('sisa');
        });
    }

    public function down(): void
    {
        Schema::table('produksi_telur', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
