<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HarvestAlertSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $cycles = DB::table('cultivation_cycles')->get();

        foreach ($cycles as $cycle) {
            if (!$cycle->estimated_harvest_date || $cycle->status === 'harvested') {
                continue;
            }

            $recipientId = DB::table('users')
                ->where('kwt_id', $cycle->kwt_id)
                ->value('id');

            foreach ([10, 7] as $daysBefore) {
                $scheduledAt = Carbon::parse($cycle->estimated_harvest_date)
                    ->subDays($daysBefore)
                    ->startOfDay();

                DB::table('harvest_alerts')->updateOrInsert(
                    [
                        'cultivation_cycle_id' => $cycle->id,
                        'alert_type' => 'h' . $daysBefore,
                    ],
                    [
                        'harvest_id' => null,
                        'scheduled_at' => $scheduledAt,
                        'sent_at' => null,
                        'channel' => 'dashboard',
                        'recipient_user_id' => $recipientId,
                        'status' => $scheduledAt->isPast() ? 'sent' : 'pending',
                        'message' => 'Pengingat panen H-' . $daysBefore . ' untuk siklus ' . $cycle->code,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }
}
