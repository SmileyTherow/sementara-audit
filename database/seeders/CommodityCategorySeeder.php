<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommodityCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = [
            [
                'name' => 'Hortikultura',
                'description' => 'Tanaman hortikultura seperti cabai dan tomat.',
            ],
            [
                'name' => 'Sayuran',
                'description' => 'Tanaman sayuran konsumsi harian.',
            ],
            [
                'name' => 'Palawija',
                'description' => 'Tanaman pangan selain padi.',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('commodity_categories')->updateOrInsert(
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
