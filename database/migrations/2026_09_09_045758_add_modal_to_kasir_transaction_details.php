<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kasir_transaction_details', function (Blueprint $table) {
            $table->decimal('modal', 15, 2)->nullable()->after('subtotal');
        });

        DB::statement('UPDATE kasir_transaction_details d JOIN products p ON d.product_id = p.id SET d.modal = p.modal WHERE d.product_id IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kasir_transaction_details', function (Blueprint $table) {
            $table->dropColumn('modal');
        });
    }
};
