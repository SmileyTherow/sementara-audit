<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcessedProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = DB::table('processed_product_categories')
            ->pluck('id', 'name');

        $kwts = DB::table('kwts')->pluck('id', 'code');

        $products = [
            [
                'name' => 'Cabai Segar KWT Mawar',
                'category' => 'Sayuran Segar',
                'kwt' => 'KWT-001',
                'price' => 35000,
                'stock' => 102,
                'unit' => 'kg',
            ],
            [
                'name' => 'Kangkung Segar KWT Melati',
                'category' => 'Sayuran Segar',
                'kwt' => 'KWT-002',
                'price' => 3000,
                'stock' => 38,
                'unit' => 'ikat',
            ],
            [
                'name' => 'Sambal Olahan KWT',
                'category' => 'Produk Olahan',
                'kwt' => 'KWT-001',
                'price' => 18000,
                'stock' => 25,
                'unit' => 'botol',
            ],
        ];

        foreach ($products as $product) {
            DB::table('processed_products')->updateOrInsert(
                ['name' => $product['name']],
                [
                    'processed_product_category_id' => $categories[$product['category']],
                    'kwt_id' => $kwts[$product['kwt']],
                    'description' => 'Produk demo siap jual.',
                    'image_path' => null,
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'unit' => $product['unit'],
                    'is_available' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
