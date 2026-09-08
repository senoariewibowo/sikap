<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportTokoandiProducts extends Command
{
    protected $signature = 'kasir:import-tokoandi';
    protected $description = 'Import produk & grup dari DB tokoandi ke modul kasir SIKAP';

    public function handle(): int
    {
        $this->info('Membersihkan data kasir lama...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('kasir_transaction_details')->truncate();
        DB::table('kasir_transactions')->truncate();
        DB::table('product_prices')->truncate();
        DB::table('products')->truncate();
        DB::table('product_groups')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Import grup produk...');
        $groups = DB::connection('tokoandi')->table('product_group')->orderBy('group_id')->get();
        $groupMap = [];
        foreach ($groups as $g) {
            $new = ProductGroup::create([
                'group_name' => $g->group_name,
                'description' => $g->description,
            ]);
            $groupMap[$g->group_id] = $new->id;
        }
        $this->info("Imported {$groups->count()} grup.");

        $this->info('Import produk...');
        $products = DB::connection('tokoandi')->table('product')->orderBy('product_id')->cursor();
        $imported = 0;
        $skipped = 0;
        $noPrice = 0;
        $seenBarcodes = [];

        foreach ($products as $p) {
            $barcode = trim($p->barcode ?? '');
            if ($barcode === '') {
                $barcode = null;
            }

            if ($barcode && isset($seenBarcodes[$barcode])) {
                $skipped++;
                continue;
            }
            if ($barcode) {
                $seenBarcodes[$barcode] = true;
            }

            $prices = [];
            for ($i = 1; $i <= 4; $i++) {
                $harga = (float) ($p->{"harga_{$i}"} ?? 0);
                $qty = (int) ($p->{"qty_{$i}"} ?? 0);
                if ($harga > 0) {
                    $prices[] = [
                        'tier' => count($prices) + 1,
                        'min_qty' => max(1, $qty),
                        'harga' => $harga,
                    ];
                }
            }

            if (empty($prices)) {
                $noPrice++;
                continue;
            }

            $modal = (float) ($p->modal ?? 0);

            $product = Product::create([
                'barcode' => $barcode,
                'product_name' => $p->product_name,
                'modal' => $modal > 0 ? $modal : null,
                'group_id' => $groupMap[$p->group_id] ?? null,
                'is_active' => true,
            ]);

            $product->prices()->createMany($prices);
            $imported++;
        }

        $this->info("Imported {$imported} produk.");
        $this->warn("Skipped {$skipped} produk (barcode duplikat).");
        $this->warn("Skipped {$noPrice} produk (tidak ada harga).");

        return self::SUCCESS;
    }
}
