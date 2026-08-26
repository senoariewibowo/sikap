<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_telur_eceran', function (Blueprint $table) {
            $table->foreignId('sortasi_telur_detail_id')->nullable()->after('gudang_id')->constrained('sortasi_telur_detail')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stok_telur_eceran', function (Blueprint $table) {
            $table->dropForeign(['sortasi_telur_detail_id']);
            $table->dropColumn('sortasi_telur_detail_id');
        });
    }
};
