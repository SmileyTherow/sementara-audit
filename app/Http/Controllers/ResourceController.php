<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Requests\ResourceRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Commodity;
use App\Models\CommodityCategory;
use App\Models\Kwt;
use App\Models\ProcessedProductCategory;
use App\Models\Role;
use App\Support\Resources;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;

class ResourceController extends Controller
{
    private function definition(string $resource): array
    {
        return Resources::get($resource);
    }

    private function authorizeResource(
        string $resource,
        bool $write = false
    ): void {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($resource === 'users') {
            Gate::authorize('manage-users');
        }

        if (str_ends_with($resource, 'categories')) {
            Gate::authorize('manage-settings');
        }

        if ($write && $user->isKoperasi()) {
            abort(403);
        }
    }

    public function index(FilterRequest $request, string $resource)
    {
        [$model, $title, $fields] = $this->definition($resource);

        $this->authorizeResource($resource);

        $query = $model::query();

        /** @var \App\Models\User $user */
        $user = $request->user();

        /*
        * Batasi data Operator KWT ke KWT miliknya sendiri.
        */
        if ($user->isOperatorKwt()) {
            if (in_array($resource, ['kwts', 'cycles', 'products'], true)) {
                $column = $resource === 'kwts' ? 'id' : 'kwt_id';

                $query->where(
                    $column,
                    $user->kwt_id ?? 0
                );
            }

            if ($resource === 'commodities') {
                $query->whereHas(
                    'cultivationCycles',
                    fn ($cycleQuery) => $cycleQuery->where(
                        'kwt_id',
                        $user->kwt_id ?? 0
                    )
                );
            }
        }

        /*
        * Pencarian umum.
        */
        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));

            $query->where(function ($search) use ($resource, $term) {
                if (in_array($resource, [
                    'kwts',
                    'commodities',
                    'cycles',
                ], true)) {
                    $search
                        ->where('name', 'like', '%' . $term . '%')
                        ->orWhere('code', 'like', '%' . $term . '%');
                }

                if ($resource === 'products') {
                    $search
                        ->where('name', 'like', '%' . $term . '%')
                        ->orWhere('description', 'like', '%' . $term . '%');
                }

                if (in_array($resource, [
                    'users',
                    'commodity-categories',
                    'product-categories',
                ], true)) {
                    $search->where(
                        'name',
                        'like',
                        '%' . $term . '%'
                    );
                }

                if ($resource === 'kwts') {
                    $search
                        ->orWhere('leader_name', 'like', '%' . $term . '%')
                        ->orWhere('phone', 'like', '%' . $term . '%')
                        ->orWhere('district', 'like', '%' . $term . '%')
                        ->orWhere('village', 'like', '%' . $term . '%')
                        ->orWhere('city', 'like', '%' . $term . '%');
                }
            });
        }

        /*
        * Filter KWT.
        */
        if ($resource === 'kwts') {
            if ($request->filled('status')) {
                $query->where(
                    'is_active',
                    $request->input('status') === 'active'
                );
            }

            if ($request->filled('district')) {
                $query->where(
                    'district',
                    'like',
                    '%' . trim($request->input('district')) . '%'
                );
            }

            if ($request->filled('village')) {
                $query->where(
                    'village',
                    'like',
                    '%' . trim($request->input('village')) . '%'
                );
            }

            if ($request->filled('kwt_id')) {
                $query->whereKey($request->input('kwt_id'));
            }
        }

        /*
        * Filter siklus budidaya.
        */
        if ($resource === 'cycles') {
            $query->with(['kwt', 'commodity']);

            if ($request->filled('kwt_id')) {
                $query->where(
                    'kwt_id',
                    $request->input('kwt_id')
                );
            }

            if ($request->filled('commodity_id')) {
                $query->where(
                    'commodity_id',
                    $request->input('commodity_id')
                );
            }

            if ($request->filled('status')) {
                $query->where(
                    'status',
                    $request->input('status')
                );
            }

            // Add period filters for cycles (planting_date)
            if ($request->filled('start_date')) {
                $query->whereDate('planting_date', '>=', $request->input('start_date'));
            }

            if ($request->filled('end_date')) {
                $query->whereDate('planting_date', '<=', $request->input('end_date'));
            }

            if ($request->filled('month')) {
                $query->whereMonth('planting_date', $request->integer('month'));
            }

            if ($request->filled('year')) {
                $query->whereYear('planting_date', $request->integer('year'));
            }
        }

        /*
        * Filter produk.
        */
        if ($resource === 'products') {
            $query->with(['kwt', 'category']);

            if ($request->filled('kwt_id')) {
                $query->where(
                    'kwt_id',
                    $request->input('kwt_id')
                );
            }
        }

        $records = $query
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.index', compact(
            'resource',
            'title',
            'fields',
            'records'
        ));
    }

    public function create(string $resource)
    {
        [$model, $title, $fields] = $this->definition($resource);

        $this->authorizeResource($resource, true);

        $record = new $model();

        return $this->form(
            $resource,
            $title,
            $fields,
            $record
        );
    }

    public function edit(string $resource, int $id)
    {
        [$model, $title, $fields] = $this->definition($resource);

        $this->authorizeResource($resource, true);

        $record = $model::findOrFail($id);

        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (
            $authUser->isOperatorKwt()
            && in_array($resource, ['cycles', 'products'], true)
            && (int) $record->kwt_id !== (int) $authUser->kwt_id
        ) {
            abort(403);
        }

        if (
            $authUser->isOperatorKwt()
            && $resource === 'kwts'
            && (int) $record->id !== (int) $authUser->kwt_id
        ) {
            abort(403);
        }

        return $this->form(
            $resource,
            $title,
            $fields,
            $record
        );
    }

    private function form(
        string $resource,
        string $title,
        array $fields,
        Model $record
    ) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $kwts = Kwt::query()
            ->when(
                $user->isOperatorKwt(),
                fn ($q) => $q->whereKey($user->kwt_id ?? 0)
            )
            ->get();

        $commodities = Commodity::query()
            ->with('category')
            ->get();

        $categories = match ($resource) {
            'commodities' => CommodityCategory::query()->get(),
            'products' => ProcessedProductCategory::query()->get(),
            default => collect(),
        };

        return view('admin.form', compact(
            'resource',
            'title',
            'fields',
            'record',
            'kwts',
            'commodities',
            'categories'
        ));
    }

    public function store(ResourceRequest $request, string $resource)
    {
        return $this->save($request, $resource);
    }

    public function update(
        ResourceRequest $request,
        string $resource,
        int $id
    ) {
        return $this->save($request, $resource, $id);
    }

    private function save(
        ResourceRequest $request,
        string $resource,
        ?int $id = null
    ) {
        [$model] = $this->definition($resource);

        $this->authorizeResource($resource, true);

        $record = $id
            ? $model::findOrFail($id)
            : new $model();

        $data = $request->validated();
        $roleId = null;

        if (
            $resource === 'products'
            && $request->hasFile('image_path')
        ) {
            $data['image_path'] = $request
                ->file('image_path')
                ->store('products', 'public');

            if (
                $record->exists
                && $record->image_path
            ) {
                Storage::disk('public')->delete(
                    $record->image_path
                );
            }
        }

        if ($resource === 'users') {
            $roleSlug = $data['role'];
            $roleId = Role::where('slug', $roleSlug)->value('id');

            if (! $roleId) {
                throw ValidationException::withMessages([
                    'role' => 'Role tidak ditemukan.',
                ]);
            }

            if (! empty($data['password'])) {
                $data['password'] = Hash::make(
                    $data['password']
                );
            } else {
                unset($data['password']);
            }

            if ($roleSlug !== 'operator_kwt') {
                $data['kwt_id'] = null;
            }

            unset($data['role']);
        }

        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (
            $resource === 'cycles'
            && $authUser->isOperatorKwt()
        ) {
            $data['kwt_id'] = $authUser->kwt_id;
        }

        if (
            $resource === 'products'
            && $authUser->isOperatorKwt()
        ) {
            $data['kwt_id'] = $authUser->kwt_id;
        }

        if ($resource === 'kwts' && empty($data['code'])) {
            $data['code'] = 'KWT-' . Str::upper(
                Str::random(6)
            );
        }

        if ($resource === 'commodities' && empty($data['code'])) {
            $data['code'] = 'COM-' . Str::upper(
                Str::random(6)
            );
        }

        $roleIdForTransaction = $roleId ?? null;

        DB::transaction(function () use (
            $record,
            $data,
            $resource,
            $roleIdForTransaction
        ) {
            $record->fill($data)->save();

            if (
                $resource === 'users'
                && $roleIdForTransaction !== null
            ) {
                $record->roles()->sync([$roleIdForTransaction]);
            }
        });

        return redirect('/admin/manage/' . $resource)
            ->with('success', 'Data berhasil disimpan.');
    }

    public function destroy(string $resource, int $id)
    {
        [$model] = $this->definition($resource);

        $this->authorizeResource($resource, true);

        $record = $model::findOrFail($id);

        if ($resource === 'users') {
            abort_if(
                (int) $id === (int) Auth::id(),
                422,
                'Akun sendiri tidak dapat dinonaktifkan.'
            );

            $record->update(['is_active' => false]);

            return back()->with(
                'success',
                'Akun dinonaktifkan.'
            );
        }

        try {
            $record->delete();
        } catch (QueryException) {
            return back()->withErrors([
                'delete' => 'Data masih digunakan dan tidak dapat dihapus.',
            ]);
        }

        return back()->with(
            'success',
            'Data berhasil dihapus.'
        );
    }
}
