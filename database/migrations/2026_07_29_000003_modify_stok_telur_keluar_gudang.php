<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->dropColumn('tujuan');

            $table->foreignId('gudang_id')->nullable()->after('id')->constrained('gudang')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->after('gudang_id')->constrained('customer')->onDelete('set null');
            $table->string('unit_jual', 10)->default('butir')->after('jumlah_butir');
        });
    }

    public function down(): void
    {
        Schema::table('stok_telur_keluar', function (Blueprint $table) {
            $table->dropIndex(['tanggal', 'gudang_id']);
            $table->dropForeign(['gudang_id']);
            $table->dropForeign(['customer_id']);
            $table->dropColumn('gudang_id');
            $table->dropColumn('customer_id');
            $table->dropColumn('unit_jual');

            $table->foreignId('kandang_id')->nullable()->after('tanggal')->constrained('kandang')->nullOnDelete();
            $table->enum('grade', ['besar', 'sedang', 'kecil', 'retak'])->after('berat_kg');
            $table->string('tujuan', 200)->nullable()->after('grade');
            $table->index(['tanggal', 'kandang_id']);
        });
    }
};
