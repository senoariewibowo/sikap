<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_telur_eceran', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('gudang_id')->nullable()->constrained('gudang')->nullOnDelete();
            $table->integer('jumlah_butir');
            $table->string('unit_jual', 10)->default('butir');
            $table->decimal('berat_kg', 8, 2)->default(0);
            $table->integer('karpet')->nullable();
            $table->integer('peti')->nullable();
            $table->string('no_referensi', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('input_by')->constrained('users');
            $table->timestamps();
        });

        $existing = DB::table('stok_telur_keluar')->where('tipe', 'eceran')->get();
        if ($existing->isNotEmpty()) {
            $data = $existing->map(fn($row) => [
                'id' => $row->id,
                'tanggal' => $row->tanggal,
                'gudang_id' => $row->gudang_id,
                'jumlah_butir' => $row->jumlah_butir,
                'unit_jual' => $row->unit_jual ?? 'butir',
                'berat_kg' => $row->berat_kg ?? 0,
                'karpet' => $row->karpet ?? null,
                'peti' => $row->peti ?? null,
                'no_referensi' => $row->no_referensi,
                'keterangan' => $row->keterangan,
                'input_by' => $row->input_by,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ])->toArray();
            DB::table('stok_telur_eceran')->insert($data);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_telur_eceran');
    }
};
