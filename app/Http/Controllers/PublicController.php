<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Models\Commodity;
use App\Models\CultivationCycle;
use App\Models\Gallery;
use App\Models\Kwt;
use App\Models\Post;
use App\Models\ProcessedProduct;
use App\Services\WhatsappMessageService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicController extends Controller
{
    private function commodities()
    {
        return Commodity::active()->whereHas('kwt', fn ($q) => $q->active())->with('kwt', 'cycles', 'availabilities');
    }

    private function productQuery()
    {
        return ProcessedProduct::where('is_active', true)->whereHas('kwt', fn ($q) => $q->active())->with('kwt');
    }

    private function postQuery()
    {
        return Post::where('status', 'published')->where('published_at', '<=', now())->where(fn ($q) => $q->whereNull('kwt_id')->orWhereHas('kwt', fn ($q) => $q->active()));
    }

    public function home()
    {
        $all = $this->commodities()->get()->filter(fn ($c) => ! in_array($c->public_state['status'], ['Tidak Aktif', 'Habis']));
        $featured = $all->sortBy(fn ($c) => array_search($c->public_state['status'], ['Ready', 'Pre-Order', 'Segera Panen', 'Sedang Ditanam']))->take(4);
        $kwts = Kwt::active()->withCount('commodities')->take(3)->get();
        $products = $this->productQuery()->take(3)->get();
        $posts = $this->postQuery()->latest('published_at')->take(3)->get();
        $schedule = CultivationCycle::active()->whereHas('kwt', fn ($q) => $q->active())->whereHas('commodity', fn ($q) => $q->active())->with('commodity', 'kwt')->orderBy('expected_harvest_date')->take(4)->get();

        return view('public.home', compact('all', 'featured', 'kwts', 'products', 'posts', 'schedule'));
    }

    public function commoditiesPage(FilterRequest $request)
    {
        // ponytail: municipal catalog statuses are filtered in memory; move predicates to SQL if catalog size makes this costly.
        $all = $this->commodities()->when($request->q, fn ($q, $v) => $q->where('name', 'like', '%'.$v.'%'))->when($request->kwt_id, fn ($q, $v) => $q->where('kwt_id', $v))->get()->filter(fn ($c) => ! in_array($c->public_state['status'], ['Tidak Aktif', 'Habis']))->filter(fn ($c) => ! $request->status || $c->public_state['status'] === $request->status);
        $records = new LengthAwarePaginator($all->forPage($request->integer('page', 1), 12)->values(), $all->count(), 12, $request->integer('page', 1), ['path' => $request->url(), 'query' => $request->query()]);

        return view('public.catalog', compact('records'));
    }

    public function commodity(Commodity $commodity)
    {
        $commodity->load('kwt', 'cycles.monitorings.photos', 'availabilities');
        $state = $commodity->public_state;
        abort_if(in_array($state['status'], ['Tidak Aktif', 'Habis']), 404);
        $cycle = $state['cycle'] ?? $commodity->cycles->sortByDesc('id')->first();

        return view('public.commodity', compact('commodity', 'state', 'cycle'));
    }

    public function kwts(FilterRequest $request)
    {
        $records = Kwt::active()->with('commodities.cycles', 'commodities.availabilities')->when($request->kwt_id, fn ($q, $v) => $q->whereKey($v))->when($request->q, fn ($q, $v) => $q->where('name', 'like', '%'.$v.'%'))->when($request->district, fn ($q, $v) => $q->where('district', $v))->when($request->village, fn ($q, $v) => $q->where('village', $v))->paginate(12)->withQueryString();

        return view('public.kwts', compact('records'));
    }

    public function kwt(Kwt $kwt)
    {
        abort_unless($kwt->status === 'active', 404);
        $kwt->load('commodities.cycles', 'commodities.availabilities', 'processedProducts');
        $commodities = $kwt->commodities->filter(fn ($c) => ! in_array($c->public_state['status'], ['Tidak Aktif', 'Habis']));
        $posts = $this->postQuery()->where('kwt_id', $kwt->id)->latest('published_at')->get();
        $galleries = Gallery::where('kwt_id', $kwt->id)->where('published_at', '<=', now())->get();

        return view('public.kwt', compact('kwt', 'commodities', 'posts', 'galleries'));
    }

    public function schedule(FilterRequest $request)
    {
        $records = CultivationCycle::active()->whereHas('kwt', fn ($q) => $q->active())->whereHas('commodity', fn ($q) => $q->active())->with('commodity', 'kwt')->when($request->q, fn ($q, $v) => $q->whereHas('commodity', fn ($c) => $c->where('name', 'like', '%'.$v.'%')))->when($request->start_date, fn ($q, $v) => $q->whereDate('expected_harvest_date', '>=', $v))->when($request->end_date, fn ($q, $v) => $q->whereDate('expected_harvest_date', '<=', $v))->when($request->village, fn ($q, $v) => $q->whereHas('kwt', fn ($k) => $k->where('village', $v)))->when($request->month, fn ($q, $v) => $q->whereMonth('expected_harvest_date', $v))->when($request->year, fn ($q, $v) => $q->whereYear('expected_harvest_date', $v))->when($request->kwt_id, fn ($q, $v) => $q->where('kwt_id', $v))->when($request->commodity_id, fn ($q, $v) => $q->where('commodity_id', $v))->when($request->district, fn ($q, $v) => $q->whereHas('kwt', fn ($k) => $k->where('district', $v)))->orderBy('expected_harvest_date')->paginate(15)->withQueryString();

        return view('public.schedule', compact('records'));
    }

    public function products(FilterRequest $request)
    {
        $records = $this->productQuery()->when($request->q, fn ($q, $v) => $q->where('name', 'like', '%'.$v.'%'))->when($request->kwt_id, fn ($q, $v) => $q->where('kwt_id', $v))->paginate(12)->withQueryString();

        return view('public.products', compact('records'));
    }

    public function product(ProcessedProduct $product)
    {
        abort_unless($product->is_active && $product->kwt?->status === 'active', 404);

        return view('public.product', compact('product'));
    }

    public function posts(FilterRequest $request)
    {
        $records = $this->postQuery()->when($request->q, fn ($q, $v) => $q->where('title', 'like', '%'.$v.'%'))->latest('published_at')->paginate(9)->withQueryString();

        return view('public.posts', compact('records'));
    }

    public function post(Post $post)
    {
        abort_unless($this->postQuery()->whereKey($post->id)->exists(), 404);

        return view('public.post', compact('post'));
    }

    public function whatsapp(Request $request, string $kind, int $id, WhatsappMessageService $service)
    {
        $data = $request->validate(['quantity' => 'required|numeric|gt:0|max:10000000']);
        if ($kind === 'commodity') {
            $product = $this->commodities()->findOrFail($id);
            $state = $product->public_state;
            abort_unless(in_array($state['status'], ['Ready', 'Pre-Order', 'Sedang Ditanam', 'Segera Panen']), 404);
            $url = match ($state['status']) {
                'Ready' => $service->buildReadyProductMessage($product, $data['quantity'], $state),
                'Pre-Order' => $service->buildPreorderMessage($product, $data['quantity'], $state),
                default => $service->buildGrowingProductMessage($product, $data['quantity'], $state),
            };
        } else {
            abort_unless($kind === 'product',404);
            $product = $this->productQuery()->findOrFail($id);
            abort_unless(in_array($product->availability_status,['available', 'limited']),404);
            $url = $service->buildProcessedProductMessage($product,$data['quantity']);
        }

        return redirect()->away($url);
    }
}
