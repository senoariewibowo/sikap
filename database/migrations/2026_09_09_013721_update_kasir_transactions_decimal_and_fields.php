<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kasir_transactions', function (Blueprint $table) {
            $table->decimal('total', 15, 2)->change();
            $table->decimal('bayar', 15, 2)->change();
            $table->decimal('kembalian', 15, 2)->change();
            $table->enum('metode_pembayaran', ['tunai', 'transfer'])->default('tunai')->after('kembalian');
            $table->decimal('dp', 15, 2)->default(0)->after('metode_pembayaran');
            $table->decimal('kekurangan', 15, 2)->default(0)->after('dp');
            $table->enum('status_pembayaran', ['lunas', 'belum_lunas'])->default('lunas')->after('kekurangan');
            $table->string('nama_pembeli', 100)->nullable()->after('status_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('kasir_transactions', function (Blueprint $table) {
            $table->double('total')->change();
            $table->double('bayar')->change();
            $table->double('kembalian')->change();
            $table->dropColumn(['metode_pembayaran', 'dp', 'kekurangan', 'status_pembayaran', 'nama_pembeli']);
        });
    }
};
