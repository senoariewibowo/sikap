<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Kandang;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kandang', function (Blueprint $table) {
            $table->string('initial', 2)->nullable()->unique()->after('nama_kandang');
        });

        $this->generateInitialsForExistingKandang();
    }

    public function down(): void
    {
        Schema::table('kandang', function (Blueprint $table) {
            $table->dropUnique(['initial']);
            $table->dropColumn('initial');
        });
    }

    private function generateInitialsForExistingKandang(): void
    {
        $kandangs = Kandang::withTrashed()->whereNull('initial')->get();
        $used = Kandang::withTrashed()->whereNotNull('initial')->pluck('initial')->toArray();

        foreach ($kandangs as $kandang) {
            $initial = $this->generateInitial($kandang->nama_kandang, $used);
            if ($initial) {
                $kandang->initial = $initial;
                $kandang->saveQuietly();
                $used[] = $initial;
            }
        }
    }

    private function generateInitial(string $nama, array $used): ?string
    {
        $nama = preg_replace('/[^A-Za-z0-9\s\-_]/', '', $nama);
        $parts = preg_split('/[\s\-_]+/', strtoupper($nama));

        if (count($parts) >= 2) {
            $initial = substr($parts[0], 0, 1) . substr($parts[1], 0, 1);
        } else {
            $initial = substr($parts[0] ?? '', 0, 2);
        }

        $initial = str_pad($initial, 2, substr($parts[0] ?? 'A', 0, 1), STR_PAD_RIGHT);

        if (!in_array($initial, $used, true)) {
            return $initial;
        }

        $first = substr($initial, 0, 1);
        $chars = array_merge(range('A', 'Z'), range('0', '9'));
        foreach ($chars as $c) {
            $candidate = $first . $c;
            if (!in_array($candidate, $used, true)) {
                return $candidate;
            }
        }

        foreach (array_merge(range('A', 'Z'), range('0', '9')) as $a) {
            foreach (array_merge(range('A', 'Z'), range('0', '9')) as $b) {
                $candidate = $a . $b;
                if (!in_array($candidate, $used, true)) {
                    return $candidate;
                }
            }
        }

        return null;
    }
};
