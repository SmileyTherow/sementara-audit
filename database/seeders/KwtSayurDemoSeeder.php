<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\CommodityCategory;
use App\Models\Kwt;
use App\Models\User;
use App\Services\CultivationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KwtSayurDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $kwt = Kwt::firstOrCreate(['slug' => 'kwt-sayur-sejahtera'], [
                'code' => 'KWT-DEMO-SAYUR',
                'name' => 'KWT Sayur Sejahtera',
                'description' => 'KWT contoh untuk demonstrasi budidaya Selada, Kol, dan Pakcoy. Profil dan angka budidaya merupakan data simulasi.',
                'address' => 'Kebun contoh, Cipondoh, Kota Tangerang',
                'district' => 'Cipondoh',
                'village' => 'Cipondoh Indah',
                'chairman_name' => 'Ibu Rina (contoh)',
                'established_year' => 2026,
                'status' => 'active',
            ]);
            $user = User::firstOrCreate(['email' => 'sayur@kwt.test'], [
                'name' => 'Pengelola Sayur Sejahtera',
                'password' => 'KwtDemo2026!',
                'role' => 'admin_kwt',
                'kwt_id' => $kwt->id,
            ]);
            $category = CommodityCategory::firstOrCreate(['name' => 'Sayur & Buah']);
            foreach ([['Selada', 35, 500, 0.2], ['Kol', 90, 300, 1.2], ['Pakcoy', 30, 600, 0.25]] as [$name, $days, $plants, $weight]) {
                $commodity = Commodity::firstOrCreate(['slug' => Str::slug($name).'-sayur-sejahtera'], [
                    'kwt_id' => $kwt->id,
                    'category_id' => $category->id,
                    'name' => $name,
                    'description' => $name.' dari kebun contoh KWT Sayur Sejahtera. Data simulasi untuk mencoba alur aplikasi.',
                    'default_growing_days' => $days,
                    'default_survival_rate' => 90,
                    'default_expected_weight_per_plant' => $weight,
                    'default_seed_reserve_percentage' => 10,
                    'yield_unit' => 'kg',
                    'cultivation_method' => 'Bedengan kebun contoh',
                    'is_active' => true,
                ]);
                if (! $commodity->cycles()->withTrashed()->exists()) {
                    app(CultivationService::class)->create([
                        'commodity_id' => $commodity->id,
                        'start_date' => now()->toDateString(),
                        'planting_holes' => $plants,
                        'seed_unit' => 'bibit',
                        'status' => 'growing',
                        'preorder_enabled' => false,
                        'person_in_charge' => $user->name,
                        'notes' => 'Siklus contoh; target merupakan simulasi, bukan rekomendasi agronomi.',
                    ], $user);
                }
            }
        });
    }
}
