<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_penjualan', function (Blueprint $table) {
            $table->enum('satuan', ['per_butir', 'per_kg', 'per_karpet', 'per_peti'])->default('per_butir')->after('stok_telur_keluar_id');
            $table->integer('jumlah_satuan')->default(0)->after('satuan');
        });

        Schema::table('penjualan_stok', function (Blueprint $table) {
            $table->foreignId('stok_telur_keluar_detail_id')->nullable()->constrained('stok_telur_keluar_details')->cascadeOnDelete()->after('stok_telur_keluar_id');
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE harga_telur MODIFY COLUMN satuan ENUM('per_butir','per_kg','per_karpet','per_peti') NOT NULL");
    }

    public function down(): void
    {
        Schema::table('transaksi_penjualan', function (Blueprint $table) {
            $table->dropColumn(['satuan', 'jumlah_satuan']);
        });

        Schema::table('penjualan_stok', function (Blueprint $table) {
            $table->dropForeign(['stok_telur_keluar_detail_id']);
            $table->dropColumn('stok_telur_keluar_detail_id');
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE harga_telur MODIFY COLUMN satuan ENUM('per_butir','per_kg','per_karpet') NOT NULL");
    }
};
