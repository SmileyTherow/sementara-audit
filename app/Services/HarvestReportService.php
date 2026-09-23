<?php

namespace App\Services;

use App\Models\CultivationCycle;
use App\Models\User;
use Illuminate\Support\Collection;

class HarvestReportService
{
    public function build(array $filters, User $user): array
    {
        $start = $filters['start_date']
            ?? now()->startOfMonth()->toDateString();

        $end = $filters['end_date']
            ?? now()->endOfMonth()->toDateString();

        $query = CultivationCycle::query()
            ->with([
                'kwt',
                'commodity',
                'harvests' => function ($query) use ($start, $end) {
                    $query->whereBetween('harvest_date', [$start, $end]);
                },
            ])
            ->where(function ($query) use ($start, $end) {
                $query
                    ->whereBetween(
                        'estimated_harvest_date',
                        [$start, $end]
                    )
                    ->orWhereHas('harvests', function ($harvestQuery) use (
                        $start,
                        $end
                    ) {
                        $harvestQuery->whereBetween(
                            'harvest_date',
                            [$start, $end]
                        );
                    });
            });

        $this->limitToUserKwt($query, $user);

        $cycles = $query
            ->when(
                $filters['q'] ?? null,
                function ($query, $value) {
                    $query->where(function ($query) use ($value) {
                        $query
                            ->where('code', 'like', "%{$value}%")
                            ->orWhereHas(
                                'commodity',
                                fn ($q) => $q->where(
                                    'name',
                                    'like',
                                    "%{$value}%"
                                )
                            )
                            ->orWhereHas(
                                'kwt',
                                fn ($q) => $q->where(
                                    'name',
                                    'like',
                                    "%{$value}%"
                                )
                            );
                    });
                }
            )
            ->when(
                $filters['kwt_id'] ?? null,
                fn ($query, $value) => $query->where('kwt_id', $value)
            )
            ->when(
                $filters['commodity_id'] ?? null,
                fn ($query, $value) => $query->where(
                    'commodity_id',
                    $value
                )
            )
            ->when(
                $filters['district'] ?? null,
                fn ($query, $value) => $query->whereHas(
                    'kwt',
                    fn ($q) => $q->where('district', $value)
                )
            )
            ->when(
                $filters['village'] ?? null,
                fn ($query, $value) => $query->whereHas(
                    'kwt',
                    fn ($q) => $q->where('village', $value)
                )
            )
            ->get();

        $rows = $cycles->map(function ($cycle) {
            $actual = (float) $cycle->harvests
                ->sum('realized_quantity');

            $expected = (float) $cycle->target_quantity;
            $unit = $cycle->target_unit
                ?: $cycle->commodity?->default_unit
                ?: 'unit';

            return [
                'kwt' => $cycle->kwt?->name ?? '-',
                'kwt_id' => $cycle->kwt_id,
                'commodity' => $cycle->commodity?->name ?? '-',
                'code' => $cycle->code,
                'seed' => (float) $cycle->seed_quantity,
                'expected' => $expected,
                'projected' => $expected,
                'actual' => $actual,
                'variance' => $actual - $expected,
                'achievement' => $expected > 0
                    ? round(($actual / $expected) * 100, 2)
                    : null,
                'unit' => $unit,
            ];
        });

        $sum = function (Collection $group): Collection {
            return $group
                ->groupBy('unit')
                ->map(fn ($unit) => [
                    'expected' => $unit->sum('expected'),
                    'projected' => $unit->sum('projected'),
                    'actual' => $unit->sum('actual'),
                ]);
        };

        $groupedByKwt = $rows->groupBy('kwt');

        return [
            'start' => $start,
            'end' => $end,
            'rows' => $rows,
            'groupedByKwt' => $groupedByKwt,
            'subtotal' => $groupedByKwt->map($sum),
            'groupedByCommodity' => $rows
                ->groupBy('commodity')
                ->map(fn ($group) => [
                    'kwts' => $group
                        ->pluck('kwt_id')
                        ->unique()
                        ->count(),
                    'totals' => $sum($group),
                ]),
            'totalsByUnit' => $sum($rows),
        ];
    }

    private function limitToUserKwt($query, User $user): void
    {
        if ($user->isOperatorKwt()) {
            $query->where('kwt_id', $user->kwt_id ?? 0);
        }
    }
}
