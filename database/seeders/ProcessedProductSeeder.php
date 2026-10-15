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
                'name' => 'Saus Tomat KWT Mawar',
                'category' => 'Produk Olahan',
                'kwt' => 'KWT-001',
                'price' => 15000,
                'stock' => 20,
                'unit' => 'botol',
                'description' => 'Saus berbahan tomat untuk pelengkap masakan, cocolan, dan aneka hidangan rumahan.',
            ],
            [
                'name' => 'Keripik Pisang KWT Melati',
                'category' => 'Produk Olahan',
                'kwt' => 'KWT-002',
                'price' => 12000,
                'stock' => 30,
                'unit' => 'bungkus',
                'description' => 'Irisan pisang yang diolah menjadi keripik renyah untuk teman bersantai atau suguhan di rumah.',
            ],
            [
                'name' => 'Sambal Olahan KWT',
                'category' => 'Produk Olahan',
                'kwt' => 'KWT-001',
                'price' => 18000,
                'stock' => 25,
                'unit' => 'botol',
                'description' => 'Sambal olahan bercita rasa pedas untuk melengkapi nasi, lauk, dan hidangan sehari-hari.',
            ],
        ];

        foreach ($products as $product) {
            DB::table('processed_products')->updateOrInsert(
                ['name' => $product['name']],
                [
                    'processed_product_category_id' => $categories[$product['category']],
                    'kwt_id' => $kwts[$product['kwt']],
                    'description' => \App\Support\CatalogDescriptions::product($product['name']) ?? $product['description'],
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
