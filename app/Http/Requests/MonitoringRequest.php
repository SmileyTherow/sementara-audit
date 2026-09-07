<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('cycle'));
    }

    public function rules(): array
    {
        $cycle = $this->route('cycle');

        return ['monitoring_date' => 'required|date|after_or_equal:'.$cycle->start_date->toDateString().'|before_or_equal:today',
            'alive_plants' => 'nullable|integer|min:0|max:'.$cycle->planting_quantity, 'dead_plants' => 'nullable|integer|min:0|max:'.$cycle->planting_quantity, 'healthy_plants' => 'required|integer|min:0|max:'.$cycle->planting_quantity, 'damaged_plants' => 'nullable|integer|min:0|max:'.$cycle->planting_quantity,
            'average_height' => 'nullable|numeric|between:0,100000', 'height_unit' => 'required|in:cm,m', 'current_estimated_weight_per_plant' => 'required|numeric|between:0,100000',
            'condition' => 'nullable|string|max:2000', 'treatment' => 'nullable|string|max:2000', 'fertilizer_notes' => 'nullable|string|max:2000', 'pest_notes' => 'nullable|string|max:2000', 'disease_notes' => 'nullable|string|max:2000', 'general_notes' => 'nullable|string|max:10000',
            'photos' => 'nullable|array|max:10', 'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096', 'is_public' => 'required|boolean', 'caption' => 'nullable|string|max:255'];
    }

    public function after(): array
    {
        return [function ($v) {
            if ($this->filled('alive_plants') && $this->healthy_plants > $this->alive_plants) {
                $v->errors()->add('healthy_plants', 'Tanaman sehat melebihi tanaman hidup.');
            }
            if ((int) $this->alive_plants + (int) $this->dead_plants > $this->route('cycle')->planting_quantity) {
                $v->errors()->add('alive_plants', 'Jumlah hidup dan mati melebihi tanaman ditanam.');
            }
            if ((int) $this->healthy_plants + (int) $this->damaged_plants > $this->route('cycle')->planting_quantity) {
                $v->errors()->add('damaged_plants', 'Jumlah sehat dan rusak melebihi tanaman ditanam.');
            }
            if (is_numeric($this->healthy_plants) && is_numeric($this->current_estimated_weight_per_plant) && $this->healthy_plants * $this->current_estimated_weight_per_plant > 99999999999) {
                $v->errors()->add('current_estimated_weight_per_plant', 'Proyeksi melampaui kapasitas pencatatan.');
            }
        }];
    }
}
