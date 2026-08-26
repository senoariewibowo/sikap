<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_telur', function (Blueprint $table) {
            $table->dropColumn(['karpet', 'peti', 'berat_kg', 'pecah', 'retak', 'kopong', 'sisa']);
        });

        DB::statement("ALTER TABLE `produksi_telur` MODIFY `status_setor` ENUM('belum_disetor','sudah_disetor') DEFAULT 'belum_disetor' AFTER `shift`");
    }

    public function down(): void
    {
        Schema::table('produksi_telur', function (Blueprint $table) {
            $table->integer('karpet')->default(0)->after('shift');
            $table->integer('peti')->default(0)->after('karpet');
            $table->decimal('berat_kg', 8, 2)->after('peti');
            $table->integer('pecah')->default(0)->after('berat_kg');
            $table->integer('retak')->default(0)->after('pecah');
            $table->integer('kopong')->default(0)->after('retak');
            $table->integer('sisa')->default(0)->after('kopong');
        });
    }
};
