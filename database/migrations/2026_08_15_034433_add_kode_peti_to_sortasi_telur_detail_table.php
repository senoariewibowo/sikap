<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SortasiTelur;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sortasi_telur_detail', function (Blueprint $table) {
            $table->string('kode_peti', 20)->nullable()->after('berat');
        });

        $this->backfillKodePeti();
    }

    public function down(): void
    {
        Schema::table('sortasi_telur_detail', function (Blueprint $table) {
            $table->dropColumn('kode_peti');
        });
    }

    private function backfillKodePeti(): void
    {
        $sortasis = SortasiTelur::with(['detail', 'kandang'])
            ->whereHas('detail', function ($q) {
                $q->whereNull('kode_peti');
            })
            ->get();

        foreach ($sortasis as $sortasi) {
            $prefix = $sortasi->kandang && $sortasi->kandang->initial
                ? $sortasi->kandang->initial
                : 'SS';
            $day = $sortasi->tanggal ? $sortasi->tanggal->format('d') : now()->format('d');

            $seq = 1;
            foreach ($sortasi->detail()->whereNull('kode_peti')->orderBy('id')->get() as $detail) {
                $detail->kode_peti = sprintf('%s-%s-%02d', $prefix, $day, $seq);
                $detail->saveQuietly();
                $seq++;
            }
        }
    }
};
