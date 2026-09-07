<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\CultivationCycle;
use App\Models\GrowthMonitoring;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CultivationService
{
    public function create(array $data, User $user): CultivationCycle
    {
        $commodity = Commodity::findOrFail($data['commodity_id']);

        return DB::transaction(function () use ($data, $commodity, $user) {
            $cycle = CultivationCycle::create(app(CultivationCalculationService::class)->calculate($data, $commodity) + ['code' => 'BDY-'.now()->year.'-'.Str::upper(Str::random(10)), 'created_by' => $user->id]);
            AuditService::record('create', $cycle);

            return $cycle;
        });
    }

    public function monitor(CultivationCycle $cycle, array $data, array $photos, User $user): GrowthMonitoring
    {
        $stored = [];
        try {
            return DB::transaction(function () use ($cycle, $data, $photos, $user, &$stored) {
                $cycle = CultivationCycle::lockForUpdate()->findOrFail($cycle->id);
                if (! in_array($cycle->status, ['seedling', 'growing', 'near_harvest', 'harvested'])) {
                    throw ValidationException::withMessages(['status' => 'Siklus ini tidak menerima monitoring.']);
                }
                $public = (bool) ($data['is_public'] ?? false);
                $caption = $data['caption'] ?? null;
                unset($data['photos'],$data['is_public'],$data['caption']);
                $data['plant_age_days'] = (int) $cycle->start_date->diffInDays(Carbon::parse($data['monitoring_date']));
                $data['projected_yield'] = round($data['healthy_plants'] * $data['current_estimated_weight_per_plant'], 4);
                $data['current_survival_rate'] = round(($data['alive_plants'] ?? $data['healthy_plants']) / $cycle->planting_quantity * 100, 4);
                $monitor = $cycle->monitorings()->create($data + ['created_by' => $user->id]);
                foreach ($photos as $photo) {
                    $path = $photo->store("kwts/{$cycle->kwt_id}/cultivation/{$cycle->id}/monitoring/{$monitor->id}", 'local');
                    $stored[] = $path;
                    $monitor->photos()->create(['file_path' => $path, 'caption' => $caption, 'taken_at' => $data['monitoring_date'], 'is_public' => $public, 'uploaded_by' => $user->id]);
                }
                $latest = $cycle->monitorings()->reorder()->orderByDesc('monitoring_date')->orderByDesc('id')->first();
                $cycle->update(['latest_projected_yield' => $latest->projected_yield]);
                AuditService::record('monitor', $monitor);

                return $monitor;
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($stored);
            throw $e;
        }
    }
}
