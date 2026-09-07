<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\CultivationCycle;
use App\Models\Harvest;
use App\Models\HarvestAvailability;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HarvestCalculationService
{
    public function create(CultivationCycle $cycle, array $data, array $photos, User $user): Harvest
    {
        $stored = [];
        try {
            return DB::transaction(function () use ($cycle, $data, $photos, $user, &$stored) {
                // Consistent lock order serializes harvest and availability updates per commodity.
                $commodity = Commodity::lockForUpdate()->findOrFail($cycle->commodity_id);
                $cycle = CultivationCycle::lockForUpdate()->findOrFail($cycle->id);
                if (! in_array($cycle->status, ['seedling', 'growing', 'near_harvest', 'harvested'])) {
                    throw ValidationException::withMessages(['status' => 'Siklus ini tidak menerima panen.']);
                }
                $details = $data['details'] ?? [];
                $caption = $data['caption'] ?? null;
                unset($data['details'],$data['photos'],$data['caption']);
                $data['damaged_quantity'] = $data['damaged_quantity'] ?? 0;
                $harvest = $cycle->harvests()->create($data + ['harvest_code' => 'PNN-'.now()->year.'-'.Str::upper(Str::random(10)), 'kwt_id' => $cycle->kwt_id, 'commodity_id' => $cycle->commodity_id, 'yield_unit' => $cycle->yield_unit, 'harvest_sequence' => ($cycle->harvests()->withTrashed()->max('harvest_sequence') ?? 0) + 1, 'average_weight_per_plant' => ! empty($data['harvested_plants']) ? $data['usable_quantity'] / $data['harvested_plants'] : 0, 'created_by' => $user->id]);
                foreach ($details as $detail) {
                    $harvest->details()->create($detail);
                }
                foreach ($photos as $photo) {
                    $path = $photo->store("kwts/{$cycle->kwt_id}/cultivation/{$cycle->id}/harvests/{$harvest->id}", 'local');
                    $stored[] = $path;
                    $harvest->photos()->create(['file_path' => $path, 'caption' => $caption, 'uploaded_by' => $user->id]);
                }
                $cycle->update(['actual_yield' => $cycle->harvests()->sum('usable_quantity'), 'status' => 'harvested']);
                $previous = $commodity->availabilities()->latest('id')->first();
                $quantity = ($previous && in_array($previous->availability_status, ['available', 'limited']) ? $previous->available_quantity : 0) + $harvest->usable_quantity;
                $this->availability($commodity, ['available_quantity' => $quantity, 'availability_status' => $quantity > 0 ? 'available' : 'sold_out', 'notes' => 'Penambahan hasil '.$harvest->harvest_code], $user, $cycle, $harvest);
                AuditService::record('harvest', $harvest);

                return $harvest;
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($stored);
            throw $e;
        }
    }

    public function availability(Commodity $commodity, array $data, User $user, ?CultivationCycle $cycle = null, ?Harvest $harvest = null): HarvestAvailability
    {
        return DB::transaction(function () use ($commodity, $data, $user, $cycle, $harvest) {
            $commodity = Commodity::lockForUpdate()->findOrFail($commodity->id);
            $record = HarvestAvailability::create($data + ['kwt_id' => $commodity->kwt_id, 'commodity_id' => $commodity->id, 'cultivation_cycle_id' => $cycle?->id, 'harvest_id' => $harvest?->id, 'unit' => $commodity->yield_unit, 'availability_updated_at' => now(), 'updated_by' => $user->id]);
            AuditService::record('availability', $record);

            return $record;
        });
    }

    public function delete(Harvest $harvest): void
    {
        DB::transaction(function () use ($harvest) {
            $commodity = Commodity::lockForUpdate()->findOrFail($harvest->commodity_id);
            $cycle = CultivationCycle::lockForUpdate()->findOrFail($harvest->cultivation_cycle_id);
            $harvest = Harvest::lockForUpdate()->findOrFail($harvest->id);
            $previous = $commodity->availabilities()->latest('id')->first();
            $quantity = max(0, ($previous?->available_quantity ?? 0) - $harvest->usable_quantity);
            $harvest->delete();
            $cycle->update(['actual_yield' => $cycle->harvests()->sum('usable_quantity')]);
            $this->availability($commodity, ['available_quantity' => $quantity, 'availability_status' => $quantity > 0 ? 'available' : 'sold_out', 'notes' => 'Koreksi pembatalan '.$harvest->harvest_code], auth()->user(), $cycle);
            AuditService::record('delete',$harvest);
        });
    }
}
