<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9+]+$/',
                'min:10',
                'max:20',
                'unique:couriers,phone',
            ],
            'email' => [
                'nullable',
                'email',
                'max:150',
                'unique:couriers,email',
            ],
            'level' => [
                'required',
                'integer',
                Rule::in([1, 2, 3, 4, 5]),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
