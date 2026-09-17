<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HarvestSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $cycles = DB::table('cultivation_cycles')->pluck('id', 'code');
        $operator1 = DB::table('users')->where('email', 'operator1@kwt.test')->value('id');
        $operator2 = DB::table('users')->where('email', 'operator2@kwt.test')->value('id');

        $harvests = [
            [
                'cycle' => 'TANAM-001',
                'date' => Carbon::today()->subDays(5),
                'predicted' => 120,
                'realized' => 132,
                'quality' => 'A',
                'created_by' => $operator1,
            ],
            [
                'cycle' => 'TANAM-003',
                'date' => Carbon::today()->subDays(5),
                'predicted' => 60,
                'realized' => 58,
                'quality' => 'A',
                'created_by' => $operator2,
            ],
        ];

        foreach ($harvests as $harvest) {
            $exists = DB::table('harvests')
                ->where('cultivation_cycle_id', $cycles[$harvest['cycle']])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('harvests')->insert([
                'cultivation_cycle_id' => $cycles[$harvest['cycle']],
                'harvest_date' => $harvest['date']->toDateString(),
                'predicted_quantity' => $harvest['predicted'],
                'realized_quantity' => $harvest['realized'],
                'variance_quantity' => $harvest['realized'] - $harvest['predicted'],
                'quality_grade' => $harvest['quality'],
                'status' => 'verified',
                'notes' => 'Data demo transaksi panen.',
                'created_by' => $harvest['created_by'],
                'verified_by' => $harvest['created_by'],
                'verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
