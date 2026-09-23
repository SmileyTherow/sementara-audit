<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class HarvestRequest extends FormRequest
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
            'harvest_date' => [
                'required',
                'date',
                'after_or_equal:' . $plantingDate,
                'before_or_equal:today',
            ],

            'predicted_quantity' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100000000',
            ],

            'realized_quantity' => [
                'required',
                'numeric',
                'min:0',
                'max:100000000',
            ],

            'quality_grade' => [
                'nullable',
                'string',
                'max:50',
            ],

            'status' => [
                'nullable',
                'in:draft,submitted,verified,rejected',
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

            'details' => [
                'nullable',
                'array',
                'max:20',
            ],

            'details.*.product_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'details.*.quantity' => [
                'required',
                'numeric',
                'min:0',
            ],

            'details.*.unit' => [
                'required',
                'string',
                'max:50',
            ],

            'details.*.quality' => [
                'nullable',
                'string',
                'max:50',
            ],

            'details.*.price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'details.*.notes' => [
                'nullable',
                'string',
                'max:1000',
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

            $predicted = $this->input('predicted_quantity');
            $realized = $this->input('realized_quantity');

            if (
                $predicted !== null
                && $realized !== null
                && (float) $realized > (float) $predicted
            ) {
                $validator->errors()->add(
                    'realized_quantity',
                    'Realisasi tidak boleh melebihi prediksi panen.'
                );
            }
        });
    }
}
