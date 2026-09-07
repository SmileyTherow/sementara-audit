<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Requests\ResourceRequest;
use App\Models\Commodity;
use App\Models\CommodityCategory;
use App\Models\Kwt;
use App\Models\ProcessedProductCategory;
use App\Services\AuditService;
use App\Services\CultivationService;
use App\Support\Resources;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResourceController extends Controller
{
    private function access(string $resource, bool $write = false): array
    {
        $definition = Resources::get($resource);
        $user = auth()->user();
        if ($resource === 'users') {
            Gate::authorize('manage-users');
        }
        if (str_ends_with($resource, 'categories')) {
            Gate::authorize('manage-settings');
        }
        if ($write && $user->role === 'admin_koperasi' && ! $user->can_manage_cultivation && in_array($resource, ['commodities', 'cycles', 'products'])) {
            abort(403);
        }

        return $definition;
    }

    public function index(FilterRequest $request, string $resource)
    {
        [$model,$title,$fields] = $this->access($resource);
        $query = $model::query();
        $user = $request->user();
        if ($user->role === 'admin_kwt') {
            $query->where($resource === 'kwts' ? 'id' : 'kwt_id', $user->kwt_id ?? 0);
        }
        $searchField = isset($fields['name']) ? 'name' : (isset($fields['title']) ? 'title' : 'code');
        $query->when($request->q, fn ($q, $v) => $q->where($searchField, 'like', '%'.$v.'%'));
        if (isset($fields['status'])) {
            $query->when($request->status, fn ($q, $v) => $q->where('status', $v));
        }
        if ($resource !== 'kwts' && ! str_ends_with($resource, 'categories')) {
            $query->when($request->kwt_id, fn ($q, $v) => $q->where('kwt_id', $v));
        }
        if ($resource === 'cycles') {
            $query->with('commodity', 'kwt')->when($request->commodity_id, fn ($q, $v) => $q->where('commodity_id', $v))->when($request->month, fn ($q, $v) => $q->whereMonth('start_date', $v))->when($request->year, fn ($q, $v) => $q->whereYear('start_date', $v));
        }
        if ($resource === 'kwts') {
            $query->when($request->district, fn ($q, $v) => $q->where('district', $v))->when($request->village, fn ($q, $v) => $q->where('village', $v));
        }
        if ($resource === 'kwts') {
            $query->when($request->kwt_id, fn ($q, $v) => $q->whereKey($v));
        }
        if ($resource === 'cycles') {
            $query->when($request->start_date, fn ($q, $v) => $q->whereDate('start_date', '>=', $v))->when($request->end_date, fn ($q, $v) => $q->whereDate('start_date', '<=', $v));
        }
        $records = $query->latest('id')->paginate(12)->withQueryString();

        return view('admin.index', compact('resource', 'title', 'fields', 'records'));
    }

    public function create(string $resource)
    {
        [$model,$title,$fields] = $this->access($resource, true);
        abort_if($resource === 'kwts' && auth()->user()->role === 'admin_kwt', 403);

        return $this->form($resource, $title, $fields, new $model);
    }

    public function edit(string $resource, int $id)
    {
        [$model,$title,$fields] = $this->access($resource, true);
        $record = $model::findOrFail($id);
        if ($resource !== 'users' && ! str_ends_with($resource, 'categories')) {
            Gate::authorize('update', $record);
        }

        return $this->form($resource, $title, $fields, $record);
    }

    private function form($resource, $title, $fields, $record)
    {
        $kwts = Kwt::query()->when(auth()->user()->role === 'admin_kwt', fn ($q) => $q->whereKey(auth()->user()->kwt_id ?? 0))->get();
        $commodities = Commodity::visibleTo(auth()->user())->get();
        $categories = $resource === 'products' ? ProcessedProductCategory::all() : CommodityCategory::all();
        if ($resource === 'cycles' && $record->exists) {
            $fields = array_intersect_key($fields, array_flip(['status', 'status_reason', 'preorder_enabled', 'person_in_charge', 'notes']));
        }

        return view('admin.form', compact('resource', 'title', 'fields', 'record', 'kwts', 'commodities', 'categories'));
    }

    public function store(ResourceRequest $request, string $resource)
    {
        return $this->save($request, $resource);
    }

    public function update(ResourceRequest $request, string $resource, int $id)
    {
        return $this->save($request, $resource, $id);
    }

    private function save(ResourceRequest $request, string $resource, ?int $id = null)
    {
        [$model] = $this->access($resource, true);
        $record = $id ? $model::findOrFail($id) : new $model;
        if ($id && $resource !== 'users' && ! str_ends_with($resource, 'categories')) {
            Gate::authorize('update', $record);
        }
        abort_if(! $id && $resource === 'kwts' && $request->user()->role === 'admin_kwt', 403);
        $data = $request->validated();
        if ($resource === 'users') {
            if (empty($data['password'])) {
                unset($data['password']);
            }
            if ($id === $request->user()->id && ($data['role'] !== 'super_admin' || ! $data['is_active'])) {
                throw ValidationException::withMessages(['role' => 'Anda tidak dapat menonaktifkan atau menurunkan peran akun sendiri.']);
            }
            if ($data['role'] !== 'admin_kwt') {
                $data['kwt_id'] = null;
            }
        }
        if ($id && $resource === 'commodities' && ($record->cycles()->exists() || $record->availabilities()->exists()) && ($record->yield_unit !== $data['yield_unit'] || (int) $record->kwt_id !== (int) $data['kwt_id'])) {
            throw ValidationException::withMessages(['yield_unit' => 'KWT dan satuan tidak dapat diubah setelah ada budidaya atau ketersediaan.']);
        }
        foreach (['image', 'logo', 'cover_image'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('kwts/'.($data['kwt_id'] ?? $record->id ?? 'central').'/'.$resource, 'public');
            } else {
                unset($data[$field]);
            }
        }
        if (! $id && in_array($resource, ['kwts', 'commodities', 'products', 'posts'])) {
            $data['slug'] = Str::slug($data['name'] ?? $data['title']).'-'.Str::lower(Str::random(6));
        }
        if ($resource === 'posts') {
            if (! $id) {
                $data['created_by'] = $request->user()->id;
            } if ($data['status'] === 'published' && empty($data['published_at'])) {
                $data['published_at'] = now();
            }
        }
        DB::transaction(function () use (&$record, $data, $resource, $id, $request) {
            if ($resource === 'cycles' && ! $id) {
                $record = app(CultivationService::class)->create(array_filter($data, fn ($v) => $v !== null), $request->user());

                return;
            }
            if ($resource === 'cycles') {
                $data = array_intersect_key($data, array_flip(['status', 'status_reason', 'preorder_enabled', 'person_in_charge', 'notes']));
            }
            $old = $record->getAttributes();
            $record->fill($data)->save();
            AuditService::record($id ? 'update' : 'create', $record, $old);
        });

        return redirect($resource === 'cycles' ? '/admin/cycles/'.$record->id : '/admin/manage/'.$resource)->with('success', 'Data berhasil disimpan.');
    }

    public function destroy(string $resource, int $id)
    {
        [$model] = $this->access($resource, true);
        $record = $model::findOrFail($id);
        if ($resource === 'users') {
            abort_if($id === auth()->id(), 422, 'Akun sendiri tidak dapat dihapus.');
            $record->update(['is_active' => false]);
        } else {
            Gate::authorize('delete', $record);
            if ($resource === 'cycles' && ($record->monitorings()->exists() || $record->harvests()->exists())) {
                return back()->withErrors(['delete' => 'Siklus dengan monitoring/panen tidak dapat dihapus. Gunakan status selesai atau batal.']);
            }
            if ($resource === 'kwts' && collect(['users', 'commodities', 'cultivation_cycles', 'harvests', 'harvest_availabilities', 'processed_products', 'posts', 'galleries'])->contains(fn ($table) => DB::table($table)->where('kwt_id', $record->id)->exists())) {
                return back()->withErrors(['delete' => 'KWT masih memiliki data atau riwayat. Nonaktifkan melalui edit profil.']);
            }
            if ($resource === 'commodities' && ($record->cycles()->withTrashed()->exists() || $record->availabilities()->exists())) {
                return back()->withErrors(['delete' => 'Komoditas memiliki riwayat. Nonaktifkan melalui edit.']);
            }
            try {
                $record->delete();
            } catch (QueryException $e) {
                return back()->withErrors(['delete' => 'Data masih digunakan dan tidak dapat dihapus.']);
            }
        }
        AuditService::record('delete',$record);

        return back()->with('success','Data berhasil dinonaktifkan/dihapus.');
    }
}
