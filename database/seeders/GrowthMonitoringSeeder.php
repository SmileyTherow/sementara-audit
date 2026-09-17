<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrowthMonitoringSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $cycles = DB::table('cultivation_cycles')->pluck('id', 'code');
        $operator = DB::table('users')
            ->where('email', 'operator1@kwt.test')
            ->value('id');

        $monitorings = [
            ['cycle' => 'TANAM-001', 'date' => 70, 'height' => 15, 'condition' => 'Baik', 'health' => 'Sehat'],
            ['cycle' => 'TANAM-001', 'date' => 50, 'height' => 35, 'condition' => 'Baik', 'health' => 'Sehat'],
            ['cycle' => 'TANAM-002', 'date' => 30, 'height' => 20, 'condition' => 'Baik', 'health' => 'Sehat'],
            ['cycle' => 'TANAM-002', 'date' => 10, 'height' => 45, 'condition' => 'Baik', 'health' => 'Sehat'],
            ['cycle' => 'TANAM-003', 'date' => 20, 'height' => 18, 'condition' => 'Baik', 'health' => 'Sehat'],
            ['cycle' => 'TANAM-004', 'date' => 5, 'height' => 10, 'condition' => 'Baik', 'health' => 'Sehat'],
        ];

        foreach ($monitorings as $monitoring) {
            $date = Carbon::today()->subDays($monitoring['date']);

            DB::table('growth_monitorings')->insert([
                'cultivation_cycle_id' => $cycles[$monitoring['cycle']],
                'monitoring_date' => $date->toDateString(),
                'plant_height' => $monitoring['height'],
                'plant_condition' => $monitoring['condition'],
                'health_status' => $monitoring['health'],
                'notes' => 'Monitoring demo pertumbuhan tanaman.',
                'created_by' => $operator,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}