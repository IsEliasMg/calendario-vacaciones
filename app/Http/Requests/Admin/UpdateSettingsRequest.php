<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'max_people_per_day' => ['required', 'integer', 'min:1', 'max:50'],
            'max_days_per_employee' => ['required', 'integer', 'min:1', 'max:365'],
            'allow_saturdays' => ['sometimes', 'boolean'],
            'allow_sundays' => ['sometimes', 'boolean'],
            'available_years' => ['required', 'array', 'min:1'],
            'available_years.*' => ['integer', 'min:2020', 'max:2100'],
        ];
    }
}
