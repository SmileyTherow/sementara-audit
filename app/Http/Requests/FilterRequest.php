<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => 'nullable|string|max:150', 'kwt_id' => 'nullable|integer|min:1', 'commodity_id' => 'nullable|integer|min:1', 'status' => 'nullable|string|max:40', 'district' => 'nullable|string|max:100', 'village' => 'nullable|string|max:100',
            'start_date' => 'nullable|date', 'end_date' => 'nullable|date|after_or_equal:start_date', 'month' => 'nullable|integer|between:1,12', 'year' => 'nullable|integer|between:1900,2100', 'page' => 'nullable|integer|min:1',
        ];
    }
}
