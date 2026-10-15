<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogExpansionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $category = DB::table('commodity_categories')->where('name', 'Sayuran')->value('id');
            $kwts = DB::table('kwts')->where('is_active', true)->orderBy('id')->pluck('id');
            if (! $category || $kwts->isEmpty()) {
                return;
            }

            $new = [
                ['SELADA', 'Selada', 35], ['MENTIMUN', 'Mentimun', 45],
                ['WORTEL', 'Wortel', 90], ['BAYAM', 'Bayam', 25],
                ['BUNCIS', 'Buncis', 55], ['KOL', 'Kol', 80],
            ];
            foreach ($new as [$code, $name, $days]) {
                if (! DB::table('commodities')->where('code', $code)->exists()) {
                    DB::table('commodities')->insert([
                        'code' => $code, 'name' => $name, 'commodity_category_id' => $category,
                        'default_unit' => 'kg', 'default_growth_days' => $days,
                        'default_harvest_days' => $days, 'is_active' => true,
                        'description' => \App\Support\CatalogDescriptions::commodity($name),
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }

            $codes = ['JAGUNG', 'TERONG', ...array_column($new, 0)];
            foreach ($codes as $index => $code) {
                $commodity = DB::table('commodities')->where('code', $code)->first();
                if (! $commodity) {
                    continue;
                }
                $cycleCode = 'DEMO-KATALOG-' . $code;
                if (DB::table('cultivation_cycles')->where('code', $cycleCode)->exists()) {
                    continue;
                }
                $days = $commodity->default_harvest_days ?: 45;
                $nearHarvest = $index % 3 === 0;
                $age = $nearHarvest ? max(1, $days - 5) : max(1, (int) ($days / 3));
                DB::table('cultivation_cycles')->insert([
                    'kwt_id' => $kwts[$index % $kwts->count()], 'commodity_id' => $commodity->id,
                    'code' => $cycleCode, 'planting_date' => today()->subDays($age)->toDateString(),
                    'estimated_harvest_date' => today()->subDays($age)->addDays($days)->toDateString(),
                    'planting_area' => 100, 'seed_quantity' => 50,
                    'target_quantity' => 50 + $index * 10, 'target_unit' => $commodity->default_unit,
                    'status' => $nearHarvest ? 'ready_to_harvest' : 'growing',
                    'notes' => 'Siklus demo untuk contoh katalog 12 komoditas.',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        });
    }
}
