<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $courier = $this->route('courier');

        return [
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:150'],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'regex:/^[0-9+]+$/',
                'min:10',
                'max:20',
                Rule::unique('couriers', 'phone')->ignore($courier),
            ],
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:150',
                Rule::unique('couriers', 'email')->ignore($courier),
            ],
            'level' => [
                'sometimes',
                'required',
                'integer',
                Rule::in([1, 2, 3, 4, 5]),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
