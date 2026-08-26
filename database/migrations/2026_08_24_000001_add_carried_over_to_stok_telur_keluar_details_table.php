<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_telur_keluar_details', function (Blueprint $table) {
            $table->unsignedBigInteger('carried_over_to_id')->nullable()->after('keterangan');
            $table->foreign('carried_over_to_id', 'stk_det_carried_over_fk')->references('id')->on('stok_telur_keluar')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('stok_telur_keluar_details', function (Blueprint $table) {
            $table->dropForeign('stk_det_carried_over_fk');
            $table->dropColumn('carried_over_to_id');
        });
    }
};
