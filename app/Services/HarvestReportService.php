<?php

namespace App\Services;

use App\Models\CultivationCycle;
use App\Models\User;

class HarvestReportService
{
    public function build(array $filters, User $user): array
    {
        $start = $filters['start_date'] ?? now()->startOfMonth()->toDateString();
        $end = $filters['end_date'] ?? now()->endOfMonth()->toDateString();
        $cycles = CultivationCycle::visibleTo($user)->with(['kwt', 'commodity', 'harvests' => fn ($q) => $q->withinPeriod($start, $end)])
            ->when($filters['q'] ?? null, fn ($q, $v) => $q->where(fn ($q) => $q->where('code', 'like', '%'.$v.'%')->orWhereHas('commodity', fn ($c) => $c->where('name', 'like', '%'.$v.'%'))->orWhereHas('kwt', fn ($k) => $k->where('name', 'like', '%'.$v.'%'))))
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('expected_harvest_date', [$start, $end])->orWhereHas('harvests', fn ($h) => $h->withinPeriod($start, $end));
            })
            ->when($filters['kwt_id'] ?? null, fn ($q, $v) => $q->where('kwt_id', $v))
            ->when($filters['commodity_id'] ?? null, fn ($q, $v) => $q->where('commodity_id', $v))
            ->when($filters['district'] ?? null, fn ($q, $v) => $q->whereHas('kwt', fn ($k) => $k->where('district', $v)))
            ->when($filters['village'] ?? null, fn ($q, $v) => $q->whereHas('kwt', fn ($k) => $k->where('village', $v)))->get();
        $rows = $cycles->map(function ($c) {
            $actual = (float) $c->harvests->sum('usable_quantity');
            $expected = (float) $c->initial_expected_yield;

            return ['kwt' => $c->kwt->name, 'kwt_id' => $c->kwt_id, 'commodity' => $c->commodity->name, 'code' => $c->code, 'seed' => (int) $c->seed_quantity, 'expected' => $expected, 'projected' => (float) $c->latest_projected_yield, 'actual' => $actual, 'variance' => $actual - $expected, 'achievement' => $expected > 0 ? round($actual / $expected * 100, 2) : null, 'unit' => $c->yield_unit];
        });
        $sum = fn ($group) => $group->groupBy('unit')->map(fn ($unit) => ['expected' => $unit->sum('expected'), 'projected' => $unit->sum('projected'), 'actual' => $unit->sum('actual')]);

        return ['start' => $start, 'end' => $end, 'rows' => $rows, 'groupedByKwt' => $rows->groupBy('kwt'), 'subtotal' => $rows->groupBy('kwt')->map($sum), 'groupedByCommodity' => $rows->groupBy('commodity')->map(fn ($r) => ['kwts' => $r->pluck('kwt_id')->unique()->count(), 'totals' => $sum($r)]), 'totalsByUnit' => $sum($rows)];
    }
}
