<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\CultivationCycle;
use App\Models\Harvest;
use App\Models\Kwt;
use App\Services\HarvestReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        HarvestReportService $reports
    ) {
        $user = $request->user();

        $cyclesQuery = CultivationCycle::query()
            ->with([
                'commodity',
                'kwt',
                'harvests',
            ])
            ->whereIn('status', [
                'planned',
                'planted',
                'growing',
                'ready_to_harvest',
            ])
            ->orderBy('estimated_harvest_date');

        $this->limitToUserKwt($cyclesQuery, $user);

        $cycles = $cyclesQuery->get();

        $commodityQuery = Commodity::query()
            ->where('is_active', true)
            ->with([
                'cultivationCycles.kwt',
                'cultivationCycles.harvests.availability',
            ]);

        if ($user->isOperatorKwt()) {
            $commodityQuery->whereHas(
                'cultivationCycles',
                fn ($query) => $query->where(
                    'kwt_id',
                    $user->kwt_id ?? 0
                )
            );
        }

        $commodities = $commodityQuery->get();

        $report = $reports->build([], $user);

        $trend = collect(range(11, 0))->map(
            function ($offset) use ($user) {
                $date = now()
                    ->startOfMonth()
                    ->subMonths($offset);

                $cycleQuery = CultivationCycle::query()
                    ->whereBetween(
                        'estimated_harvest_date',
                        [
                            $date->toDateString(),
                            $date->copy()->endOfMonth()->toDateString(),
                        ]
                    );

                $this->limitToUserKwt($cycleQuery, $user);

                $expected = $cycleQuery
                    ->get()
                    ->groupBy('target_unit')
                    ->map(
                        fn ($group) => (float) $group
                            ->sum('target_quantity')
                    );

                $harvestQuery = Harvest::query()
                    ->whereBetween(
                        'harvest_date',
                        [
                            $date->toDateString(),
                            $date->copy()->endOfMonth()->toDateString(),
                        ]
                    )
                    ->with('cultivationCycle');

                if ($user->isOperatorKwt()) {
                    $harvestQuery->whereHas(
                        'cultivationCycle',
                        fn ($query) => $query->where(
                            'kwt_id',
                            $user->kwt_id ?? 0
                        )
                    );
                }

                $actual = $harvestQuery
                    ->get()
                    ->groupBy(
                        fn ($harvest) => $harvest
                            ->cultivationCycle
                            ?->target_unit ?? 'unit'
                    )
                    ->map(
                        fn ($group) => (float) $group
                            ->sum('realized_quantity')
                    );

                return [
                    'label' => $date->translatedFormat('M Y'),
                    'expected' => $expected,
                    'actual' => $actual,
                ];
            }
        );

        $kwtQuery = Kwt::query()
            ->where('is_active', true);

        if ($user->isOperatorKwt()) {
            $kwtQuery->whereKey($user->kwt_id ?? 0);
        }

        $kwtCount = $kwtQuery->count();

        return view('admin.dashboard', compact(
            'cycles',
            'commodities',
            'report',
            'trend',
            'kwtCount'
        ));
    }

    private function limitToUserKwt($query, $user): void
    {
        if ($user->isOperatorKwt()) {
            $query->where('kwt_id', $user->kwt_id ?? 0);
        }
    }
}
