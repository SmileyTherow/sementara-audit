<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HarvestDetailSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $harvests = DB::table('harvests')
            ->join('cultivation_cycles', 'cultivation_cycles.id', '=', 'harvests.cultivation_cycle_id')
            ->pluck('harvests.id', 'cultivation_cycles.code');

        $details = [
            ['cycle' => 'TANAM-001', 'product' => 'Cabai Segar', 'quantity' => 132, 'unit' => 'kg', 'quality' => 'A', 'price' => 35000],
            ['cycle' => 'TANAM-003', 'product' => 'Kangkung Segar', 'quantity' => 58, 'unit' => 'ikat', 'quality' => 'A', 'price' => 3000],
        ];

        foreach ($details as $detail) {
            $harvestId = $harvests[$detail['cycle']] ?? null;

            if (!$harvestId) {
                continue;
            }

            $exists = DB::table('harvest_details')
                ->where('harvest_id', $harvestId)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('harvest_details')->insert([
                'harvest_id' => $harvestId,
                'product_name' => $detail['product'],
                'quantity' => $detail['quantity'],
                'unit' => $detail['unit'],
                'quality' => $detail['quality'],
                'price' => $detail['price'],
                'notes' => 'Detail hasil panen demo.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
