<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CultivationCycleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $today = Carbon::today();

        $kwts = DB::table('kwts')->pluck('id', 'code');
        $commodities = DB::table('commodities')->pluck('id', 'code');
        $operators = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.slug', 'operator_kwt')
            ->pluck('users.id', 'users.email');

        $cycles = [
            [
                'code' => 'TANAM-001',
                'kwt' => 'KWT-001',
                'commodity' => 'CABAI',
                'days_ago' => 80,
                'status' => 'harvested',
                'target_quantity' => 120,
                'target_unit' => 'kg',
                'area' => 250,
                'operator' => 'operator1@kwt.test',
            ],
            [
                'code' => 'TANAM-002',
                'kwt' => 'KWT-001',
                'commodity' => 'TOMAT',
                'days_ago' => 45,
                'status' => 'growing',
                'target_quantity' => 200,
                'target_unit' => 'kg',
                'area' => 300,
                'operator' => 'operator1@kwt.test',
            ],
            [
                'code' => 'TANAM-003',
                'kwt' => 'KWT-002',
                'commodity' => 'KANGKUNG',
                'days_ago' => 30,
                'status' => 'harvested',
                'target_quantity' => 60,
                'target_unit' => 'ikat',
                'area' => 100,
                'operator' => 'operator2@kwt.test',
            ],
            [
                'code' => 'TANAM-004',
                'kwt' => 'KWT-002',
                'commodity' => 'SAWI',
                'days_ago' => 10,
                'status' => 'growing',
                'target_quantity' => 100,
                'target_unit' => 'ikat',
                'area' => 150,
                'operator' => 'operator2@kwt.test',
            ],
            [
                'code' => 'TANAM-005',
                'kwt' => 'KWT-003',
                'commodity' => 'CABAI',
                'days_ago' => 15,
                'status' => 'planted',
                'target_quantity' => 90,
                'target_unit' => 'kg',
                'area' => 200,
                'operator' => null,
            ],
            [
                'code' => 'TANAM-006',
                'kwt' => 'KWT-004',
                'commodity' => 'TOMAT',
                'days_ago' => 5,
                'status' => 'planned',
                'target_quantity' => 150,
                'target_unit' => 'kg',
                'area' => 220,
                'operator' => null,
            ],
        ];

        foreach ($cycles as $cycle) {
            $plantingDate = $today->copy()->subDays($cycle['days_ago']);
            $harvestDate = $plantingDate->copy()->addDays(
                $cycle['commodity'] === 'CABAI' ? 75 :
                ($cycle['commodity'] === 'TOMAT' ? 65 :
                ($cycle['commodity'] === 'KANGKUNG' ? 25 : 28))
            );

            $operatorId = $cycle['operator']
                ? ($operators[$cycle['operator']] ?? null)
                : null;

            DB::table('cultivation_cycles')->updateOrInsert(
                ['code' => $cycle['code']],
                [
                    'kwt_id' => $kwts[$cycle['kwt']],
                    'commodity_id' => $commodities[$cycle['commodity']],
                    'planting_date' => $plantingDate->toDateString(),
                    'estimated_harvest_date' => $harvestDate->toDateString(),
                    'actual_harvest_date' => $cycle['status'] === 'harvested'
                        ? $harvestDate->toDateString()
                        : null,
                    'planting_area' => $cycle['area'],
                    'seed_quantity' => 10,
                    'target_quantity' => $cycle['target_quantity'],
                    'target_unit' => $cycle['target_unit'],
                    'status' => $cycle['status'],
                    'notes' => 'Data demo transaksi tanam.',
                    'created_by' => $operatorId,
                    'updated_by' => $operatorId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
