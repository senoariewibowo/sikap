<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_penjualan', function (Blueprint $table) {
            $table->foreignId('ttd_petugas')->nullable()->after('input_by')->constrained('users')->nullOnDelete();
            $table->timestamp('ttd_petugas_at')->nullable()->after('ttd_petugas');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_penjualan', function (Blueprint $table) {
            $table->dropForeign(['ttd_petugas']);
            $table->dropColumn(['ttd_petugas', 'ttd_petugas_at']);
        });
    }
};
