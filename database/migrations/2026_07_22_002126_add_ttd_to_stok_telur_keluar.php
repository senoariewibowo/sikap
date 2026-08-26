<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->foreignId('ttd_pengirim')->nullable()->after('input_by')->constrained('users')->nullOnDelete();
            $table->timestamp('ttd_pengirim_at')->nullable()->after('ttd_pengirim');
            $table->foreignId('ttd_mengetahui')->nullable()->after('ttd_pengirim_at')->constrained('users')->nullOnDelete();
            $table->timestamp('ttd_mengetahui_at')->nullable()->after('ttd_mengetahui');
        });
    }

    public function down(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->dropForeign(['ttd_pengirim']);
            $table->dropForeign(['ttd_mengetahui']);
            $table->dropColumn(['ttd_pengirim', 'ttd_pengirim_at', 'ttd_mengetahui', 'ttd_mengetahui_at']);
        });
    }
};
