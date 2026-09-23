<?php

namespace App\Http\Requests;

use App\Support\Resources;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_active;
    }

    public function rules(): array
    {
        $resource = $this->route('resource');
        $id = $this->route('id');

        [$model, , $rules] = Resources::get($resource);

        if ($resource === 'users') {
            $rules['email'] = [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($id),
            ];

            if (! $id) {
                $rules['password'] = 'required|string|min:8|max:255';
            }
        }

        if ($resource === 'kwts') {
            $rules['code'] = [
                'required',
                'string',
                'max:50',
                Rule::unique('kwts', 'code')->ignore($id),
            ];
        }

        if ($resource === 'commodities') {
            $rules['code'] = [
                'nullable',
                'string',
                'max:50',
                Rule::unique('commodities', 'code')->ignore($id),
            ];
        }

        if ($resource === 'cycles') {
            $rules['code'] = [
                'required',
                'string',
                'max:50',
                Rule::unique('cultivation_cycles', 'code')->ignore($id),
            ];
        }

        if (in_array($resource, [
            'commodity-categories',
            'product-categories',
        ], true)) {
            $rules['name'] = [
                'required',
                'string',
                'max:100',
                Rule::unique((new $model)->getTable(), 'name')->ignore($id),
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];

        foreach (array_keys($this->rules()) as $field) {
            $attributes[$field] = Resources::label($field);
        }

        return $attributes;
    }
}
