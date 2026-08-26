<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_telur', function (Blueprint $table) {
            $table->dropColumn('grade');
            $table->integer('karpet')->nullable()->after('shift');
            $table->integer('peti')->nullable()->after('karpet');
            $table->integer('pecah')->nullable()->after('peti');
            $table->integer('retak')->nullable()->after('pecah');
            $table->integer('kopong')->nullable()->after('retak');
            $table->integer('sisa')->nullable()->after('kopong');
        });
    }

    public function down(): void
    {
        Schema::table('produksi_telur', function (Blueprint $table) {
            $table->dropColumn(['karpet', 'peti', 'pecah', 'retak', 'kopong', 'sisa']);
            $table->enum('grade', ['besar', 'sedang', 'kecil', 'retak'])->after('berat_kg');
        });
    }
};
