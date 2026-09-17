<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommoditySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = DB::table('commodity_categories')
            ->pluck('id', 'name');

        $commodities = [
            [
                'code' => 'CABAI',
                'name' => 'Cabai',
                'category' => 'Hortikultura',
                'default_unit' => 'kg',
                'default_growth_days' => 90,
                'default_harvest_days' => 75,
            ],
            [
                'code' => 'TOMAT',
                'name' => 'Tomat',
                'category' => 'Hortikultura',
                'default_unit' => 'kg',
                'default_growth_days' => 80,
                'default_harvest_days' => 65,
            ],
            [
                'code' => 'KANGKUNG',
                'name' => 'Kangkung',
                'category' => 'Sayuran',
                'default_unit' => 'ikat',
                'default_growth_days' => 30,
                'default_harvest_days' => 25,
            ],
            [
                'code' => 'SAWI',
                'name' => 'Sawi',
                'category' => 'Sayuran',
                'default_unit' => 'ikat',
                'default_growth_days' => 35,
                'default_harvest_days' => 28,
            ],
            [
                'code' => 'JAGUNG',
                'name' => 'Jagung',
                'category' => 'Palawija',
                'default_unit' => 'kg',
                'default_growth_days' => 100,
                'default_harvest_days' => 90,
            ],
            [
                'code' => 'TERONG',
                'name' => 'Terong',
                'category' => 'Hortikultura',
                'default_unit' => 'kg',
                'default_growth_days' => 80,
                'default_harvest_days' => 65,
            ],
        ];

        foreach ($commodities as $commodity) {
            $categoryId = $categories[$commodity['category']] ?? null;

            if (!$categoryId) {
                continue;
            }

            DB::table('commodities')->updateOrInsert(
                ['code' => $commodity['code']],
                [
                    'commodity_category_id' => $categoryId,
                    'name' => $commodity['name'],
                    'default_unit' => $commodity['default_unit'],
                    'default_growth_days' => $commodity['default_growth_days'],
                    'default_harvest_days' => $commodity['default_harvest_days'],
                    'description' => 'Data demo tanaman ' . $commodity['name'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
