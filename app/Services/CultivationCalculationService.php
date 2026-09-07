<?php

namespace App\Services;

use App\Models\Commodity;
use Carbon\Carbon;

class CultivationCalculationService
{
    public function calculate(array $data, Commodity $commodity): array
    {
        $reserve = $data['reserve_seed_percentage'] ?? $commodity->default_seed_reserve_percentage;
        $planting = (int) ($data['planting_holes'] ?? $data['planting_quantity'] ?? $data['seed_quantity']);
        $extra = (int) ceil($planting * $reserve / 100);
        $survival = $data['target_survival_rate'] ?? $commodity->default_survival_rate;
        $productive = (int) floor($planting * $survival / 100);
        $weight = $data['target_weight_per_plant'] ?? $commodity->default_expected_weight_per_plant;

        return array_replace($data, [
            'kwt_id' => $commodity->kwt_id, 'commodity_id' => $commodity->id, 'yield_unit' => $commodity->yield_unit,
            'planting_quantity' => $planting, 'reserve_seed_percentage' => $reserve, 'reserve_seed_quantity' => $extra,
            'seed_quantity' => isset($data['planting_holes']) ? $planting + $extra : (int) $data['seed_quantity'],
            'target_survival_rate' => $survival, 'target_productive_plants' => $productive, 'target_weight_per_plant' => $weight,
            'initial_expected_yield' => round($productive * $weight, 4), 'latest_projected_yield' => round($productive * $weight, 4),
            'expected_harvest_date' => $data['expected_harvest_date'] ?? Carbon::parse($data['start_date'])->addDays($commodity->default_growing_days)->toDateString(),
            'seed_total_cost' => isset($data['seed_price']) ? ($data['planting_holes'] ?? null ? $planting + $extra : $data['seed_quantity']) * $data['seed_price'] : null,
        ]);
    }
}
