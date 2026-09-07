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
    public function __invoke(Request $request, HarvestReportService $reports)
    {
        $user = $request->user();
        $cycles = CultivationCycle::visibleTo($user)->with('commodity', 'kwt')->active()->orderBy('expected_harvest_date')->get();
        $commodities = Commodity::visibleTo($user)->with('kwt', 'cycles', 'availabilities')->get();
        $report = $reports->build([], $user);
        $trend = collect(range(11, 0))->map(function ($offset) use ($user) {
            $date = now()->startOfMonth()->subMonths($offset);
            $expected = CultivationCycle::visibleTo($user)->whereBetween('expected_harvest_date', [$date->toDateString(), $date->copy()->endOfMonth()->toDateString()])->selectRaw('yield_unit, SUM(initial_expected_yield) as total')->groupBy('yield_unit')->pluck('total', 'yield_unit');
            $actual = Harvest::visibleTo($user)->withinPeriod($date->toDateString(), $date->copy()->endOfMonth()->toDateString())->selectRaw('yield_unit, SUM(usable_quantity) as total')->groupBy('yield_unit')->pluck('total', 'yield_unit');

            return ['label' => $date->translatedFormat('M Y'), 'expected' => $expected, 'actual' => $actual];
        });
        $kwtCount = Kwt::active()->when($user->role === 'admin_kwt', fn ($q) => $q->whereKey($user->kwt_id ?? 0))->count();

        return view('admin.dashboard', compact('cycles', 'commodities', 'report', 'trend', 'kwtCount'));
    }
}
