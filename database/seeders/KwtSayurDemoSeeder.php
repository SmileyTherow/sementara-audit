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
            // Cari atau buat KWT berdasarkan kode unik (tidak lagi berdasarkan slug
            // karena migration kwts tidak memiliki kolom slug)
            $kwt = Kwt::firstOrCreate(
                ['code' => 'KWT-DEMO-SAYUR'],
                [
                    'code' => 'KWT-DEMO-SAYUR',
                    'name' => 'KWT Sayur Sejahtera',
                    // migration kwts saat ini memiliki kolom leader_name, bukan chairman_name
                    'leader_name' => 'Ibu Rina (contoh)',
                    'address' => 'Kebun contoh, Cipondoh, Kota Tangerang',
                    'district' => 'Cipondoh',
                    'village' => 'Cipondoh Indah',
                    // set active agar demo terlihat di publik
                    'is_active' => true,
                ]
            );

            $user = User::firstOrCreate(['email' => 'sayur@kwt.test'], [
                'name' => 'Pengelola Sayur Sejahtera',
                'password' => 'KwtDemo2026!',
                'role' => 'admin_kwt',
                'kwt_id' => $kwt->id,
            ]);

            $category = CommodityCategory::firstOrCreate(['name' => 'Sayur & Buah']);

            foreach ([['Selada', 35, 500, 0.2], ['Kol', 90, 300, 1.2], ['Pakcoy', 30, 600, 0.25]] as [$name, $days, $plants, $weight]) {
                // Cari atau buat Commodity berdasarkan kwt_id + name agar tidak mengandalkan kolom slug
                $commodity = Commodity::firstOrCreate(
                    ['kwt_id' => $kwt->id, 'name' => $name],
                    [
                        // migration commodities mengharuskan commodity_category_id (foreign key)
                        'commodity_category_id' => $category->id,
                        // sesuaikan nama kolom dengan migration: default_growth_days bukan default_growing_days
                        'default_growth_days' => $days,
                        // default_unit ada pada migration; gunakan 'kg' untuk contoh
                        'default_unit' => 'kg',
                        'description' => $name . ' dari kebun contoh KWT Sayur Sejahtera. Data simulasi untuk mencoba alur aplikasi.',
                        'is_active' => true,
                    ]
                );

                // Pastikan siklus hanya dibuat jika belum ada (menggunakan service)
                if (! $commodity->cultivationCycles()->withTrashed()->exists()) {
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