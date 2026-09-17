<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            KwtSeeder::class,
            UserSeeder::class,

            CommodityCategorySeeder::class,
            CommoditySeeder::class,

            CultivationCycleSeeder::class,
            GrowthMonitoringSeeder::class,

            HarvestSeeder::class,
            HarvestDetailSeeder::class,
            HarvestAvailabilitySeeder::class,
            HarvestAlertSeeder::class,

            ProcessedProductCategorySeeder::class,
            ProcessedProductSeeder::class,

            SettingSeeder::class,
        ]);
    }
}
