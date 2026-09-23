<?php

namespace App\Services;

use App\Models\Commodity;
use Carbon\Carbon;

class CultivationCalculationService
{
    public function calculate(
        array $data,
        Commodity $commodity
    ): array {
        $plantingDate = Carbon::parse(
            $data['planting_date'] ?? now()->toDateString()
        );

        $estimatedHarvestDate = $data['estimated_harvest_date']
            ?? $plantingDate
                ->copy()
                ->addDays((int) ($commodity->default_growth_days ?? 0))
                ->toDateString();

        $targetUnit = $data['target_unit']
            ?? $commodity->default_unit;

        return [
            'kwt_id' => $data['kwt_id'],
            'commodity_id' => $commodity->id,
            'code' => $data['code'],
            'planting_date' => $plantingDate->toDateString(),
            'estimated_harvest_date' => $estimatedHarvestDate,
            'actual_harvest_date' => $data['actual_harvest_date'] ?? null,
            'planting_area' => $data['planting_area'] ?? null,
            'seed_quantity' => $data['seed_quantity'] ?? null,
            'target_quantity' => $data['target_quantity'] ?? null,
            'target_unit' => $targetUnit,
            'status' => $data['status'] ?? 'planned',
            'notes' => $data['notes'] ?? null,
        ];
    }
}
