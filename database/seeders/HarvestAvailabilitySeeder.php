<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HarvestAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $harvests = DB::table('harvests')
            ->join('cultivation_cycles', 'cultivation_cycles.id', '=', 'harvests.cultivation_cycle_id')
            ->pluck('harvests.id', 'cultivation_cycles.code');

        $availability = [
            ['cycle' => 'TANAM-001', 'quantity' => 132, 'reserved' => 20, 'sold' => 30, 'unit' => 'kg'],
            ['cycle' => 'TANAM-003', 'quantity' => 58, 'reserved' => 10, 'sold' => 20, 'unit' => 'ikat'],
        ];

        foreach ($availability as $item) {
            $harvestId = $harvests[$item['cycle']] ?? null;

            if (!$harvestId) {
                continue;
            }

            DB::table('harvest_availabilities')->updateOrInsert(
                ['harvest_id' => $harvestId],
                [
                    'available_quantity' => $item['quantity'],
                    'reserved_quantity' => $item['reserved'],
                    'sold_quantity' => $item['sold'],
                    'unit' => $item['unit'],
                    'available_from' => Carbon::today()->subDays(5)->toDateString(),
                    'available_until' => Carbon::today()->addDays(30)->toDateString(),
                    'status' => 'available',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
