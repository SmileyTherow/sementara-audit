<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Requests\HarvestRequest;
use App\Http\Requests\MonitoringRequest;
use App\Models\CultivationCycle;
use App\Models\GrowthMonitoring;
use App\Models\GrowthMonitoringPhoto;
use App\Models\Harvest;
use App\Models\HarvestPhoto;
use App\Models\Kwt;
use App\Models\Commodity;
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
        Gate::authorize('update', $cycle);

        $service->monitor($cycle, $request->validated(), $request->file('photos', []), $request->user());

        return back()->with('success', 'Monitoring dan dokumentasi berhasil disimpan.');
    }

    public function harvest(
        HarvestRequest $request,
        CultivationCycle $cycle,
        HarvestCalculationService $service
    ) {
        Gate::authorize('update', $cycle);

        $service->create(
            $cycle,
            $request->validated(),
            $request->file('photos', []),
            $request->user()
        );

        return back()->with(
            'success',
            'Panen dicatat dan ketersediaan ditambahkan.'
        );
    }

    public function deleteHarvest(
        Harvest $harvest,
        HarvestCalculationService $service
    ) {
        Gate::authorize('delete', $harvest);

        // service->delete accepts only the Harvest model
        $service->delete($harvest);

        return back()->with(
            'success',
            'Panen berhasil dihapus.'
        );
    }

    public function availability(
        Request $request,
        Harvest $harvest,
        HarvestCalculationService $service
    ) {
        $availability = $harvest->availability()
            ->firstOrNew();

        $availability->setRelation('harvest', $harvest);

        Gate::authorize('update', $availability);

        $data = $request->validate([
            'available_quantity' => ['required','numeric','min:0','max:100000000'],
            'reserved_quantity' => ['required','numeric','min:0','max:100000000'],
            'sold_quantity' => ['required','numeric','min:0','max:100000000'],
            'unit' => ['required','string','max:50'],
            'available_from' => ['nullable','date'],
            'available_until' => ['nullable','date','after_or_equal:available_from'],
            'status' => ['required','in:available,limited,sold_out,not_available'],
        ]);

        if (in_array($data['status'], ['sold_out','not_available'], true)) {
            $data['available_quantity'] = 0;
        }

        $harvest->loadMissing('cultivationCycle.commodity');

        $commodity = $harvest->cultivationCycle->commodity ?? null;
        $cycle = $harvest->cultivationCycle ?? null;

        // Use availability(...) from the service (matches service implementation)
        $service->availability(
            $commodity,
            $data,
            $request->user(),
            $cycle,
            $harvest
        );

        return back()->with('success', 'Ketersediaan panen berhasil disimpan.');
    }

    public function harvests(FilterRequest $request)
    {
        $user = $request->user();

        $request->validate([
            'q' => 'nullable|string|max:150',
            'kwt_id' => 'nullable|integer|min:1',
            'commodity_id' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|between:1900,2100',
            'status' => 'nullable|string|max:40',
        ]);

        $query = Harvest::query()
            ->with(['cultivationCycle.commodity','cultivationCycle.kwt','availability','photos'])
            ->whereHas('cultivationCycle', function ($cycleQuery) use ($user, $request) {
                // Restrict to user's KWT if operator
                if ($user->isOperatorKwt()) {
                    $cycleQuery->where('kwt_id', $user->kwt_id ?? 0);
                }

                if ($request->filled('kwt_id')) {
                    $cycleQuery->where('kwt_id', $request->integer('kwt_id'));
                }

                // NEW: filter by commodity_id selected in the filter component
                if ($request->filled('commodity_id')) {
                    $cycleQuery->where('commodity_id', $request->integer('commodity_id'));
                }
            });

        // Apply q across harvest notes and cycle/commodity/kwt (avoid non-existent columns)
        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));

            $query->where(function ($q) use ($term) {
                $q->where('notes', 'like', '%' . $term . '%')
                  ->orWhereHas('cultivationCycle', function ($cycleQuery) use ($term) {
                      $cycleQuery->where('code', 'like', '%' . $term . '%')
                          ->orWhereHas('commodity', fn($cq) => $cq->where('name', 'like', '%' . $term . '%'))
                          ->orWhereHas('kwt', fn($kq) => $kq->where('name', 'like', '%' . $term . '%'));
                  });
            });
        }

        $query = $query
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('harvest_date','>=',$request->input('start_date')))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('harvest_date','<=',$request->input('end_date')))
            ->when($request->filled('month'), fn($q) => $q->whereMonth('harvest_date',$request->integer('month')))
            ->when($request->filled('year'), fn($q) => $q->whereYear('harvest_date',$request->integer('year')))
            ->when($request->filled('status'), fn($q) => $q->where('status',$request->input('status')));

        $records = $query->latest('harvest_date')->latest('id')->paginate(20)->withQueryString();

        // Keep compatibility for views: ensure cultivationCycle relation is present
        $records->getCollection()->transform(function ($item) {
            if (! $item->relationLoaded('cultivationCycle') || $item->cultivationCycle === null) {
                $stubCycle = new CultivationCycle();
                $stubKwt = new Kwt(['name' => '-']);
                $stubCommodity = new Commodity(['name' => '-']);
                $stubCycle->setRelation('kwt', $stubKwt);
                $stubCycle->setRelation('commodity', $stubCommodity);
                $item->setRelation('cultivationCycle', $stubCycle);
            } else {
                if (! $item->cultivationCycle->kwt) {
                    $item->cultivationCycle->setRelation('kwt', new Kwt(['name' => '-']));
                }
                if (! $item->cultivationCycle->commodity) {
                    $item->cultivationCycle->setRelation('commodity', new Commodity(['name' => '-']));
                }
            }
            return $item;
        });

        return view('admin.harvests', compact('records'));
    }

    public function monitorings(FilterRequest $request)
    {
        $user = $request->user();

        $request->validate([
            'q' => 'nullable|string|max:150',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2000|max:2100',
            'kwt_id' => 'nullable|integer|exists:kwts,id',
            'commodity_id' => 'nullable|integer|min:1',
        ]);

        $query = GrowthMonitoring::query()
            ->with(['cultivationCycle.commodity','cultivationCycle.kwt','photos'])
            ->whereHas('cultivationCycle', function ($cycleQuery) use ($request, $user) {
                if ($user->isOperatorKwt()) {
                    $cycleQuery->where('kwt_id', $user->kwt_id ?? 0);
                }

                if ($request->filled('kwt_id')) {
                    $cycleQuery->where('kwt_id', $request->integer('kwt_id'));
                }

                // NEW: filter by commodity_id when provided
                if ($request->filled('commodity_id')) {
                    $cycleQuery->where('commodity_id', $request->integer('commodity_id'));
                }

                if ($request->filled('start_date')) {
                    $cycleQuery->whereDate('planting_date', '<=', $request->input('end_date') ?? now()->toDateString());
                }
            });

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function ($q) use ($term) {
                $q->where('notes', 'like', '%' . $term . '%')
                  ->orWhereHas('cultivationCycle', function ($cycleQuery) use ($term) {
                      $cycleQuery->where('code', 'like', '%' . $term . '%')
                          ->orWhereHas('commodity', fn($cq) => $cq->where('name', 'like', '%' . $term . '%'))
                          ->orWhereHas('kwt', fn($kq) => $kq->where('name', 'like', '%' . $term . '%'));
                  });
            });
        }

        $query = $query
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('monitoring_date','>=',$request->input('start_date')))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('monitoring_date','<=',$request->input('end_date')))
            ->when($request->filled('month'), fn($q) => $q->whereMonth('monitoring_date',$request->integer('month')))
            ->when($request->filled('year'), fn($q) => $q->whereYear('monitoring_date',$request->integer('year')));

        $records = $query->latest('monitoring_date')->latest('id')->paginate(20)->withQueryString();

        // Ensure relations for view rendering
        $records->getCollection()->transform(function ($item) {
            if (! $item->relationLoaded('cultivationCycle') || $item->cultivationCycle === null) {
                $stubCycle = new CultivationCycle();
                $stubKwt = new Kwt(['name' => '-']);
                $stubCommodity = new Commodity(['name' => '-']);
                $stubCycle->setRelation('kwt', $stubKwt);
                $stubCycle->setRelation('commodity', $stubCommodity);
                $item->setRelation('cultivationCycle', $stubCycle);
            } else {
                if (! $item->cultivationCycle->kwt) {
                    $item->cultivationCycle->setRelation('kwt', new Kwt(['name' => '-']));
                }
                if (! $item->cultivationCycle->commodity) {
                    $item->cultivationCycle->setRelation('commodity', new Commodity(['name' => '-']));
                }
            }
            return $item;
        });

        return view('admin.monitorings', compact('records'));
    }

    public function photo(string $kind, int $id)
    {
        if ($kind === 'monitoring') {
            $photo = GrowthMonitoringPhoto::with('monitoring.cycle.kwt')->findOrFail($id);
            $cycle = $photo->monitoring->cycle;
            $public = $photo->is_public && $cycle && $cycle->kwt?->status === 'active' && $cycle->commodity?->is_active;
        } else {
            abort_unless($kind === 'harvest', 404);
            $photo = HarvestPhoto::findOrFail($id);
            $cycle = Harvest::find($photo->harvest_id)?->cycle;
            $public = false;
        }
        abort_unless($cycle, 404);
        if (!$public) {
            Gate::authorize('view', $cycle);
        }

        $path = Storage::disk('local')->path($photo->file_path);

        abort_unless(is_file($path), 404);

        if (!$public) {
            Gate::authorize('view', $cycle);
        }

        $path = Storage::disk('local')->path($photo->file_path);

        return response()->file($path, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
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
        Gate::authorize('update', $cycle);
        $path = $photo->file_path;
        $photo->delete();
        Storage::disk('local')->delete($path);

        return back()->with('success', 'Foto dihapus.');
    }
}