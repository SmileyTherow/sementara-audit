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

        if (
            Auth::user()->isOperatorKwt()
            && in_array($resource, ['cycles', 'products'], true)
            && (int) $record->kwt_id !== (int) Auth::user()->kwt_id
        ) {
            abort(403);
        }

        if (
            Auth::user()->isOperatorKwt()
            && $resource === 'kwts'
            && (int) $record->id !== (int) Auth::user()->kwt_id
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
        $record
    ) {
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

        $newPhotos = [];
        $oldPhotos = [];
        if (in_array($resource, ['products', 'commodities'], true)) {
            try {
                foreach (['image_path', 'image_path_2', 'image_path_3', 'image_path_4', 'image_path_5'] as $field) {
                    if ($request->hasFile($field)) {
                        $data[$field] = $request->file($field)->store($resource, 'public');
                        $newPhotos[] = $data[$field];
                        if ($record->$field) {
                            $oldPhotos[] = $record->$field;
                        }
                    } else {
                        unset($data[$field]);
                    }
                }
            } catch (\Throwable $exception) {
                Storage::disk('public')->delete($newPhotos);
                throw $exception;
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

        if (
            $resource === 'cycles'
            && Auth::user()->isOperatorKwt()
        ) {
            $data['kwt_id'] = Auth::user()->kwt_id;
        }

        if (
            $resource === 'products'
            && Auth::user()->isOperatorKwt()
        ) {
            $data['kwt_id'] = Auth::user()->kwt_id;
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

        try {
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
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($newPhotos);
            throw $exception;
        }
        Storage::disk('public')->delete($oldPhotos);

        return redirect(url('/admin/manage/' . $resource))
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
