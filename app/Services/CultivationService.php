<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\CultivationCycle;
use App\Models\GrowthMonitoring;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CultivationService
{
    public function create(
        array $data,
        User $user
    ): CultivationCycle {
        $commodity = Commodity::findOrFail($data['commodity_id']);

        if (
            $user->isOperatorKwt()
            && (int) $data['kwt_id'] !== (int) $user->kwt_id
        ) {
            abort(403);
        }

        return DB::transaction(function () use (
            $data,
            $commodity,
            $user
        ) {
            $cycleData = app(
                CultivationCalculationService::class
            )->calculate(
                array_merge($data, [
                    'code' => $data['code']
                        ?? 'BDY-' . now()->format('Y') . '-'
                        . Str::upper(Str::random(8)),
                ]),
                $commodity
            );

            $cycleData['created_by'] = $user->id;

            $cycle = CultivationCycle::create($cycleData);

            AuditService::record('create', $cycle);

            return $cycle;
        });
    }

    public function monitor(
        CultivationCycle $cycle,
        array $data,
        array $photos,
        User $user
    ): GrowthMonitoring {
        $stored = [];

        try {
            return DB::transaction(function () use (
                $cycle,
                $data,
                $photos,
                $user,
                &$stored
            ) {
                $cycle = CultivationCycle::query()
                    ->with('kwt')
                    ->lockForUpdate()
                    ->findOrFail($cycle->id);

                if (! $user->can('update', $cycle)) {
                    abort(403);
                }

                if (! in_array($cycle->status, [
                    'planned',
                    'planted',
                    'growing',
                    'ready_to_harvest',
                ], true)) {
                    throw ValidationException::withMessages([
                        'status' => 'Siklus ini tidak menerima monitoring.',
                    ]);
                }

                $caption = $data['caption'] ?? null;

                unset(
                    $data['photos'],
                    $data['caption']
                );

                $monitoring = $cycle
                    ->growthMonitorings()
                    ->create([
                        'monitoring_date' => $data['monitoring_date'],
                        'plant_height' => $data['plant_height'] ?? null,
                        'plant_condition' => $data['plant_condition'] ?? null,
                        'health_status' => $data['health_status'] ?? null,
                        'notes' => $data['notes'] ?? null,
                        'created_by' => $user->id,
                    ]);

                foreach ($photos as $photo) {
                    $path = $photo->store(
                        "kwts/{$cycle->kwt_id}/cultivation/"
                        . "{$cycle->id}/monitoring/"
                        . $monitoring->id,
                        'local'
                    );

                    $stored[] = $path;

                    $monitoring->photos()->create([
                        'file_path' => $path,
                        'caption' => $caption,
                    ]);
                }

                if ($cycle->status === 'planned') {
                    $cycle->update([
                        'status' => 'growing',
                        'updated_by' => $user->id,
                    ]);
                } elseif ($cycle->status === 'planted') {
                    $cycle->update([
                        'status' => 'growing',
                        'updated_by' => $user->id,
                    ]);
                }

                AuditService::record('monitor', $monitoring);

                return $monitoring;
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($stored);

            throw $exception;
        }
    }
}
