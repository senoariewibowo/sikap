<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setoran_telur', function (Blueprint $table) {
            $table->dropColumn(['pecah', 'retak', 'kopong', 'sisa']);
        });
    }

    public function down(): void
    {
        Schema::table('setoran_telur', function (Blueprint $table) {
            $table->integer('pecah')->default(0)->after('butir');
            $table->integer('retak')->default(0)->after('pecah');
            $table->integer('kopong')->default(0)->after('retak');
            $table->integer('sisa')->default(0)->after('kopong');
        });
    }
};
