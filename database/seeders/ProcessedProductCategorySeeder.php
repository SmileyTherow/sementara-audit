<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcessedProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = [
            [
                'name' => 'Sayuran Segar',
                'description' => 'Produk hasil panen segar.',
            ],
            [
                'name' => 'Produk Olahan',
                'description' => 'Produk hasil olahan KWT.',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('processed_product_categories')->updateOrInsert(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
