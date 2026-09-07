<?php

namespace App\Http\Requests;

use App\Support\Resources;
use Illuminate\Foundation\Http\FormRequest;

class HarvestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('cycle'));
    }

    public function rules(): array
    {
        return [
            'harvest_date' => 'required|date|after_or_equal:'.$this->route('cycle')->start_date->toDateString().'|before_or_equal:today',
            'gross_quantity' => 'required|numeric|between:0,100000000', 'usable_quantity' => 'required|numeric|min:0|lte:gross_quantity', 'damaged_quantity' => 'nullable|numeric|min:0|lte:gross_quantity', 'harvested_plants' => 'nullable|integer|min:1|max:'.$this->route('cycle')->planting_quantity,
            'notes' => 'nullable|string|max:10000', 'photos' => 'nullable|array|max:10', 'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096', 'caption' => 'nullable|string|max:255',
            'details' => 'nullable|array|max:20', 'details.*.grade' => 'required|string|max:100', 'details.*.size_label' => 'nullable|string|max:100', 'details.*.quantity' => 'required|numeric|min:0', 'details.*.weight' => 'nullable|numeric|min:0', 'details.*.unit' => 'required|in:'.Resources::UNITS, 'details.*.notes' => 'nullable|string|max:1000',
        ];
    }

    public function after(): array
    {
        return [function ($v) {
            if ((float) $this->usable_quantity + (float) $this->damaged_quantity > (float) $this->gross_quantity + 0.00001) {
                $v->errors()->add('usable_quantity', 'Hasil layak dan rusak melebihi panen kotor.');
            }
        }];
    }
}
