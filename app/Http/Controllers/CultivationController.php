<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Requests\HarvestRequest;
use App\Http\Requests\MonitoringRequest;
use App\Models\Commodity;
use App\Models\CultivationCycle;
use App\Models\GrowthMonitoring;
use App\Models\GrowthMonitoringPhoto;
use App\Models\Harvest;
use App\Models\HarvestPhoto;
use App\Services\CultivationService;
use App\Services\HarvestCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CultivationController extends Controller
{
    public function show(CultivationCycle $cycle)
    {
        Gate::authorize('view', $cycle);
        $cycle->load('kwt', 'commodity', 'monitorings.photos', 'harvests.photos', 'harvests.details');

        return view('admin.cycle', compact('cycle'));
    }

    public function monitor(MonitoringRequest $request, CultivationCycle $cycle, CultivationService $service)
    {
        $service->monitor($cycle, $request->validated(), $request->file('photos', []), $request->user());

        return back()->with('success', 'Monitoring dan dokumentasi berhasil disimpan.');
    }

    public function harvest(HarvestRequest $request, CultivationCycle $cycle, HarvestCalculationService $service)
    {
        $service->create($cycle, $request->validated(), $request->file('photos', []), $request->user());

        return back()->with('success', 'Panen dicatat dan ketersediaan ditambahkan.');
    }

    public function deleteHarvest(Harvest $harvest, HarvestCalculationService $service)
    {
        Gate::authorize('delete', $harvest);
        $service->delete($harvest);

        return back()->with('success', 'Panen dibatalkan. Realisasi dan ketersediaan diperbarui.');
    }

    public function availability(Request $request, Commodity $commodity, HarvestCalculationService $service)
    {
        abort_unless($request->user()->role === 'admin_koperasi' || $request->user()->can('update', $commodity), 403);
        $data = $request->validate(['available_quantity' => 'required|numeric|between:0,100000000', 'availability_status' => 'required|in:available,limited,sold_out,not_available', 'notes' => 'nullable|string|max:2000']);
        if (in_array($data['availability_status'], ['sold_out', 'not_available'])) {
            $data['available_quantity'] = 0;
        }
        $service->availability($commodity, $data, $request->user());

        return back()->with('success', 'Ketersediaan terbaru berhasil disimpan.');
    }

    public function harvests(FilterRequest $request)
    {
        $request->validate(['start_date' => 'nullable|date', 'end_date' => 'nullable|date|after_or_equal:start_date', 'month' => 'nullable|integer|between:1,12', 'year' => 'nullable|integer|between:1900,2100']);
        $records = Harvest::visibleTo($request->user())->with('commodity', 'kwt', 'cycle')->when($request->q, fn ($q, $v) => $q->where('harvest_code', 'like', '%'.$v.'%'))->when($request->kwt_id, fn ($q, $v) => $q->where('kwt_id', $v))->when($request->commodity_id, fn ($q, $v) => $q->where('commodity_id', $v))->when($request->start_date, fn ($q, $v) => $q->whereDate('harvest_date', '>=', $v))->when($request->end_date, fn ($q, $v) => $q->whereDate('harvest_date', '<=', $v))->when($request->month, fn ($q, $v) => $q->whereMonth('harvest_date', $v))->when($request->year, fn ($q, $v) => $q->whereYear('harvest_date', $v))->latest('harvest_date')->paginate(15)->withQueryString();

        return view('admin.harvests', compact('records'));
    }

    public function monitorings(FilterRequest $request)
    {
        $records = GrowthMonitoring::with('cycle.commodity', 'cycle.kwt')->whereHas('cycle', fn ($q) => $q->visibleTo($request->user())->when($request->kwt_id, fn ($q, $v) => $q->where('kwt_id', $v)))->when($request->q, fn ($q, $v) => $q->where('general_notes', 'like', '%'.$v.'%'))->latest('monitoring_date')->paginate(15)->withQueryString();

        return view('admin.monitorings', compact('records'));
    }

    public function photo(string $kind, int $id)
    {
        if ($kind === 'monitoring') {
            $photo = GrowthMonitoringPhoto::with('monitoring.cycle.kwt')->findOrFail($id);
            $cycle = $photo->monitoring->cycle;
            $public = $photo->is_public && $cycle && $cycle->kwt?->is_active && $cycle->commodity?->is_active;
        } else {
            abort_unless($kind === 'harvest', 404);
            $photo = HarvestPhoto::findOrFail($id);
            $cycle = Harvest::find($photo->harvest_id)?->cycle;
            $public = false;
        }
        abort_unless($cycle, 404);
        if (! $public) {
            Gate::authorize('view', $cycle);
        }

        return Storage::disk('local')->response($photo->file_path, null, ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store']);
    }

    public function deletePhoto(string $kind, int $id)
    {
        if ($kind === 'monitoring') {
            $photo = GrowthMonitoringPhoto::findOrFail($id);
            $cycle = $photo->monitoring->cycle;
        } else {
            abort_unless($kind === 'harvest', 404);
            $photo = HarvestPhoto::findOrFail($id);
            $cycle = Harvest::findOrFail($photo->harvest_id)->cycle;
        }
        Gate::authorize('update',$cycle);
        $path = $photo->file_path;
        $photo->delete();
        Storage::disk('local')->delete($path);

        return back()->with('success','Foto dihapus.');
    }
}
