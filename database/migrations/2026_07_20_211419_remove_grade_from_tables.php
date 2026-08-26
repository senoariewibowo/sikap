<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_telur_keluar', fn(Blueprint $t) => $t->dropColumn('grade'));
        Schema::table('harga_telur', fn(Blueprint $t) => $t->dropColumn('grade'));
        Schema::table('transaksi_penjualan', fn(Blueprint $t) => $t->dropColumn('grade'));
    }

    public function down(): void
    {
        Schema::table('stok_telur_keluar', fn(Blueprint $t) => $t->enum('grade', ['besar', 'sedang', 'kecil', 'retak'])->after('berat_kg'));
        Schema::table('harga_telur', fn(Blueprint $t) => $t->enum('grade', ['besar', 'sedang', 'kecil', 'retak'])->after('satuan'));
        Schema::table('transaksi_penjualan', fn(Blueprint $t) => $t->enum('grade', ['besar', 'sedang', 'kecil', 'retak'])->after('stok_telur_keluar_id'));
    }
};
