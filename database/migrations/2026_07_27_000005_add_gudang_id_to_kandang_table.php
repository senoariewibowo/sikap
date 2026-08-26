<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kandang', function (Blueprint $table) {
            $table->foreignId('gudang_id')->nullable()->after('id')->constrained('gudang')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kandang', function (Blueprint $table) {
            $table->dropForeign(['gudang_id']);
            $table->dropColumn('gudang_id');
        });
    }
};
