<?php

declare(strict_types=1);

namespace App\Http\Requests\Guest;

use Illuminate\Foundation\Http\FormRequest;

class RegisterVacationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('employee_id');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'dates' => ['nullable', 'array'],
            'dates.*' => ['required', 'date', 'date_format:Y-m-d'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'dates.*.date_format' => 'El formato de fecha no es válido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'dates' => $this->input('dates', []),
        ]);
    }
}
