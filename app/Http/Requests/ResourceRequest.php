<?php

namespace App\Http\Requests;

use App\Models\Commodity;
use App\Models\CultivationCycle;
use App\Models\Kwt;
use App\Services\CultivationCalculationService;
use App\Support\Resources;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_active ?? false;
    }

    public function rules(): array
    {
        [$model,,$rules] = Resources::get($this->route('resource'));
        $id = $this->route('id');
        if ($id && $this->route('resource') === 'cycles') {
            $rules = array_intersect_key($rules, array_flip(['status', 'status_reason', 'preorder_enabled', 'person_in_charge', 'notes']));
        }
        if ($this->user()->role === 'admin_kwt' && isset($rules['kwt_id'])) {
            $this->merge(['kwt_id' => $this->user()->kwt_id]);
        }
        if ($this->route('resource') === 'users') {
            $rules['email'] = ['required', 'email', 'max:150', Rule::unique('users')->ignore($id)];
            if (! $id) {
                $rules['password'] = 'required|string|min:10|max:100';
            }
        }
        if ($this->route('resource') === 'kwts') {
            $rules['code'] = ['required', 'string', 'max:40', Rule::unique('kwts')->ignore($id)];
        }
        if (str_ends_with($this->route('resource'), 'categories')) {
            $rules['name'] = ['required', 'string', 'max:100', Rule::unique((new $model)->getTable())->ignore($id)];
        }
        if ($this->route('resource') === 'galleries' && ! $id) {
            $rules['image'] = 'required|image|mimes:jpg,jpeg,png,webp|max:4096';
        }

        return $rules;
    }

    public function after(): array
    {
        return [function ($validator) {
            if ($this->filled('kwt_id') && ! Kwt::find($this->kwt_id)) {
                $validator->errors()->add('kwt_id', 'KWT tidak tersedia.');
            }
            if ($this->route('resource') === 'cycles') {
                $commodity = $this->route('id') ? null : Commodity::find($this->commodity_id);
                if (! $this->route('id') && (! $commodity || ! $this->user()->can('update', $commodity))) {
                    $validator->errors()->add('commodity_id', 'Komoditas tidak dapat dikelola oleh akun Anda.');
                }
                if (! $this->planting_holes && $this->planting_quantity > $this->seed_quantity) {
                    $validator->errors()->add('planting_quantity', 'Bibit ditanam tidak boleh melebihi jumlah bibit.');
                }
                if (! $this->route('id') && $commodity && ! $validator->errors()->any()) {
                    $calculated = app(CultivationCalculationService::class)->calculate(array_filter($this->validated(), fn ($v) => $v !== null), $commodity);
                    if ($calculated['initial_expected_yield'] > 99999999999) {
                        $validator->errors()->add('target_weight_per_plant', 'Ekspektasi melampaui kapasitas pencatatan. Kurangi jumlah bibit atau target hasil.');
                    }
                    if (($calculated['seed_total_cost'] ?? 0) > 9999999999999) {
                        $validator->errors()->add('seed_price', 'Total biaya bibit melampaui kapasitas pencatatan.');
                    }
                }
                if ($id = $this->route('id')) {
                    $cycle = CultivationCycle::findOrFail($id);
                    if ($cycle->status !== $this->status && ! $this->filled('status_reason')) {
                        $validator->errors()->add('status_reason', 'Isi alasan perubahan status.');
                    }
                }
            }
        }];
    }

    public function attributes(): array
    {
        return collect($this->rules())->mapWithKeys(fn ($v, $k) => [$k => Resources::label($k)])->all();
    }
}
