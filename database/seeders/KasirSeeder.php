<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Database\Seeder;

class KasirSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['group_name' => 'Makanan Ringan', 'description' => 'Snack & cemilan'],
            ['group_name' => 'Minuman', 'description' => 'Minuman dingin & panas'],
            ['group_name' => 'Kebutuhan Pokok', 'description' => 'Sembako'],
        ];

        foreach ($groups as $g) {
            ProductGroup::firstOrCreate(['group_name' => $g['group_name']], $g);
        }

        $snack = ProductGroup::where('group_name', 'Makanan Ringan')->first();
        $minum = ProductGroup::where('group_name', 'Minuman')->first();
        $pokok = ProductGroup::where('group_name', 'Kebutuhan Pokok')->first();

        $products = [
            [
                'barcode' => '8991002100001', 'product_name' => 'Keripik Kentang 68g', 'modal' => 8000,
                'group_id' => $snack->id,
                'prices' => [
                    ['tier' => 1, 'min_qty' => 1, 'harga' => 12000],
                    ['tier' => 2, 'min_qty' => 6, 'harga' => 11000],
                    ['tier' => 3, 'min_qty' => 24, 'harga' => 10000],
                ],
            ],
            [
                'barcode' => '8991002100002', 'product_name' => 'Biskuit Coklat 120g', 'modal' => 9000,
                'group_id' => $snack->id,
                'prices' => [
                    ['tier' => 1, 'min_qty' => 1, 'harga' => 13000],
                    ['tier' => 2, 'min_qty' => 12, 'harga' => 12000],
                ],
            ],
            [
                'barcode' => '8991002100003', 'product_name' => 'Wafer Vanila 90g', 'modal' => 7000,
                'group_id' => $snack->id,
                'prices' => [
                    ['tier' => 1, 'min_qty' => 1, 'harga' => 10000],
                    ['tier' => 2, 'min_qty' => 6, 'harga' => 9000],
                    ['tier' => 3, 'min_qty' => 24, 'harga' => 8500],
                    ['tier' => 4, 'min_qty' => 48, 'harga' => 8000],
                ],
            ],
            [
                'barcode' => '8991002100004', 'product_name' => 'Air Mineral 600ml', 'modal' => 2000,
                'group_id' => $minum->id,
                'prices' => [
                    ['tier' => 1, 'min_qty' => 1, 'harga' => 4000],
                    ['tier' => 2, 'min_qty' => 12, 'harga' => 3500],
                ],
            ],
            [
                'barcode' => '8991002100005', 'product_name' => 'Teh Botol 350ml', 'modal' => 3000,
                'group_id' => $minum->id,
                'prices' => [
                    ['tier' => 1, 'min_qty' => 1, 'harga' => 5000],
                    ['tier' => 2, 'min_qty' => 12, 'harga' => 4500],
                ],
            ],
            [
                'barcode' => '8991002100006', 'product_name' => 'Kopi Sachet', 'modal' => 1200,
                'group_id' => $minum->id,
                'prices' => [
                    ['tier' => 1, 'min_qty' => 1, 'harga' => 2000],
                    ['tier' => 2, 'min_qty' => 10, 'harga' => 1800],
                    ['tier' => 3, 'min_qty' => 50, 'harga' => 1500],
                ],
            ],
            [
                'barcode' => '8991002100007', 'product_name' => 'Gula Pasir 1kg', 'modal' => 15000,
                'group_id' => $pokok->id,
                'prices' => [
                    ['tier' => 1, 'min_qty' => 1, 'harga' => 18000],
                    ['tier' => 2, 'min_qty' => 5, 'harga' => 17500],
                ],
            ],
            [
                'barcode' => '8991002100008', 'product_name' => 'Minyak Goreng 1L', 'modal' => 16000,
                'group_id' => $pokok->id,
                'prices' => [
                    ['tier' => 1, 'min_qty' => 1, 'harga' => 19000],
                    ['tier' => 2, 'min_qty' => 6, 'harga' => 18500],
                    ['tier' => 3, 'min_qty' => 12, 'harga' => 18000],
                ],
            ],
        ];

        foreach ($products as $data) {
            $prices = $data['prices'];
            unset($data['prices']);

            $product = Product::firstOrCreate(
                ['barcode' => $data['barcode']],
                $data
            );

            if ($product->prices()->count() === 0) {
                foreach ($prices as $p) {
                    $product->prices()->create($p);
                }
            }
        }
    }
}
