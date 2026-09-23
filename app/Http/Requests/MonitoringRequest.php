<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class MonitoringRequest extends FormRequest
{
    public function authorize(): bool
    {
        $cycle = $this->route('cycle');

        return $cycle
            && $this->user()
            && $this->user()->can('update', $cycle);
    }

    public function rules(): array
    {
        $cycle = $this->route('cycle');
        $plantingDate = $cycle?->planting_date?->toDateString() ?? 'today';

        return [
            'monitoring_date' => [
                'required',
                'date',
                'after_or_equal:' . $plantingDate,
                'before_or_equal:today',
            ],

            'plant_height' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100000',
            ],

            'plant_condition' => [
                'nullable',
                'string',
                'max:100',
            ],

            'health_status' => [
                'nullable',
                'string',
                'max:100',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'photos' => [
                'nullable',
                'array',
                'max:10',
            ],

            'photos.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'caption' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $cycle = $this->route('cycle');

            if (! $cycle) {
                return;
            }

            $monitoringDate = $this->date('monitoring_date');

            if (
                $monitoringDate
                && $cycle->estimated_harvest_date
                && $monitoringDate->greaterThan(
                    $cycle->estimated_harvest_date
                )
            ) {
                $validator->errors()->add(
                    'monitoring_date',
                    'Tanggal monitoring melewati estimasi panen.'
                );
            }
        });
    }
}
