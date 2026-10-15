<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Models\Commodity;
use App\Models\CultivationCycle;
use App\Models\Kwt;
use App\Models\ProcessedProduct;
use App\Services\WhatsappMessageService;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    private function commodityQuery()
    {
        return Commodity::query()
            ->where('is_active', true)
            ->whereHas('cultivationCycles.kwt', function ($query) {
                $query->where('is_active', true);
            })
            ->with([
                'category',
                'cultivationCycles' => function ($query) {
                    $query->with([
                        'kwt',
                        'harvests.availability',
                        'growthMonitorings.photos',
                    ])->latest('id');
                },
            ]);
    }

    private function productQuery()
    {
        return ProcessedProduct::query()
            ->where('is_available', true)
            ->whereHas('kwt', function ($query) {
                $query->where('is_active', true);
            })
            ->with(['kwt', 'category']);
    }

    public function home()
    {
        $all = $this->commodityQuery()
            ->get()
            ->filter(fn (Commodity $commodity) => ! in_array(
                $commodity->public_state['status'],
                ['Tidak Aktif', 'Habis'],
                true
            ));

        $featured = $all
            ->sortBy(function (Commodity $commodity) {
                return array_search(
                    $commodity->public_state['status'],
                    ['Ready', 'Pre-Order', 'Segera Panen', 'Sedang Ditanam'],
                    true
                );
            })
            ->take(12);

        $kwts = Kwt::query()
            ->where('is_active', true)
            ->withCount('cultivationCycles')
            ->take(3)
            ->get();

        $products = $this->productQuery()
            ->take(3)
            ->get();

        $schedule = CultivationCycle::query()
            ->whereIn('status', [
                'planned',
                'planted',
                'growing',
                'ready_to_harvest',
            ])
            ->whereNotNull('estimated_harvest_date')
            ->whereHas('kwt', fn ($query) => $query->where('is_active', true))
            ->with(['commodity', 'kwt'])
            ->orderBy('estimated_harvest_date')
            ->take(4)
            ->get();

        // Fitur posts belum tersedia pada schema baru.
        $posts = collect();

        return view('public.home', compact(
            'all',
            'featured',
            'kwts',
            'products',
            'posts',
            'schedule'
        ));
    }

    public function commoditiesPage(FilterRequest $request)
    {
        $records = $this->commodityQuery()
            ->when($request->q, fn ($query, $value) => $query->where(
                'name',
                'like',
                '%' . $value . '%'
            ))
            ->get()
            ->filter(function (Commodity $commodity) use ($request) {
                $status = $commodity->public_state['status'];

                return ! in_array($status, ['Tidak Aktif', 'Habis'], true)
                    && (! $request->status || $status === $request->status);
            })
            ->values();

        return view('public.catalog', compact('records'));
    }

    public function commodity(Commodity $commodity)
    {
        abort_unless($commodity->is_active, 404);

        $commodity->load([
            'category',
            'cultivationCycles.kwt',
            'cultivationCycles.harvests.availability',
            'cultivationCycles.growthMonitorings.photos',
        ]);

        $state = $commodity->public_state;

        abort_if(
            in_array($state['status'], ['Tidak Aktif', 'Habis'], true),
            404
        );

        $cycle = $state['cycle']
            ?? $commodity->cultivationCycles->sortByDesc('id')->first();

        return view('public.commodity', compact(
            'commodity',
            'state',
            'cycle'
        ));
    }

    public function kwts(FilterRequest $request)
    {
        $records = Kwt::query()
            ->where('is_active', true)
            ->with([
                'cultivationCycles.commodity',
                'processedProducts',
            ])
            ->when(
                $request->kwt_id,
                fn ($query, $value) => $query->whereKey($value)
            )
            ->when(
                $request->q,
                fn ($query, $value) => $query->where(
                    'name',
                    'like',
                    '%' . $value . '%'
                )
            )
            ->when(
                $request->district,
                fn ($query, $value) => $query->where('district', $value)
            )
            ->when(
                $request->village,
                fn ($query, $value) => $query->where('village', $value)
            )
            ->paginate(12)
            ->withQueryString();

        return view('public.kwts', compact('records'));
    }

    public function kwt(Kwt $kwt)
    {
        abort_unless($kwt->is_active, 404);

        $kwt->load([
            'cultivationCycles.commodity',
            'processedProducts.category',
        ]);

        $commodities = $kwt->cultivationCycles
            ->pluck('commodity')
            ->unique('id')
            ->filter(fn (Commodity $commodity) => $commodity->is_active)
            ->values();

        $posts = collect();
        $galleries = collect();

        return view('public.kwt', compact(
            'kwt',
            'commodities',
            'posts',
            'galleries'
        ));
    }

    public function schedule(FilterRequest $request)
    {
        $records = CultivationCycle::query()
            ->whereIn('status', [
                'planned',
                'planted',
                'growing',
                'ready_to_harvest',
            ])
            ->whereNotNull('estimated_harvest_date')
            ->whereHas('kwt', fn ($query) => $query->where('is_active', true))
            ->whereHas('commodity', fn ($query) => $query->where('is_active', true))
            ->with(['commodity', 'kwt'])
            ->when(
                $request->q,
                fn ($query, $value) => $query->whereHas(
                    'commodity',
                    fn ($commodity) => $commodity->where(
                        'name',
                        'like',
                        '%' . $value . '%'
                    )
                )
            )
            ->when(
                $request->start_date,
                fn ($query, $value) => $query->whereDate(
                    'estimated_harvest_date',
                    '>=',
                    $value
                )
            )
            ->when(
                $request->end_date,
                fn ($query, $value) => $query->whereDate(
                    'estimated_harvest_date',
                    '<=',
                    $value
                )
            )
            ->when(
                $request->month,
                fn ($query, $value) => $query->whereMonth(
                    'estimated_harvest_date',
                    $value
                )
            )
            ->when(
                $request->year,
                fn ($query, $value) => $query->whereYear(
                    'estimated_harvest_date',
                    $value
                )
            )
            ->when(
                $request->kwt_id,
                fn ($query, $value) => $query->where('kwt_id', $value)
            )
            ->when(
                $request->commodity_id,
                fn ($query, $value) => $query->where(
                    'commodity_id',
                    $value
                )
            )
            ->orderBy('estimated_harvest_date')
            ->paginate(15)
            ->withQueryString();

        return view('public.schedule', compact('records'));
    }

    public function products(FilterRequest $request)
    {
        $records = $this->productQuery()
            ->when(
                $request->q,
                fn ($query, $value) => $query->where(
                    'name',
                    'like',
                    '%' . $value . '%'
                )
            )
            ->when(
                $request->kwt_id,
                fn ($query, $value) => $query->where(
                    'kwt_id',
                    $value
                )
            )
            ->paginate(15)
            ->withQueryString();

        return view('public.products', compact('records'));
    }

    public function product(ProcessedProduct $product)
    {
        abort_unless(
            $product->is_available && $product->kwt?->is_active,
            404
        );

        $product->load(['kwt', 'category']);

        return view('public.product', compact('product'));
    }

    public function whatsapp(
        Request $request,
        string $kind,
        int $id,
        WhatsappMessageService $service
    ) {
        $data = $request->validate([
            'quantity' => ['required', 'numeric', 'gt:0', 'max:10000000'],
        ]);

        if ($kind === 'product') {
            $product = $this->productQuery()->findOrFail($id);

            $url = $service->buildProcessedProductMessage(
                $product,
                $data['quantity']
            );

            return redirect()->away($url);
        }

        abort_unless($kind === 'commodity', 404);

        $commodity = $this->commodityQuery()->findOrFail($id);
        $state = $commodity->public_state;

        abort_unless(
            in_array(
                $state['status'],
                ['Ready', 'Pre-Order', 'Sedang Ditanam', 'Segera Panen'],
                true
            ),
            404
        );

        $url = $service->buildCommodityMessage(
            $commodity,
            $data['quantity'],
            $state
        );

        return redirect()->away($url);
    }

    public function posts()
    {
        abort(404);
    }

    public function post()
    {
        abort(404);
    }
}
