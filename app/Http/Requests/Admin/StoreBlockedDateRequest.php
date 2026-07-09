<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlockedDateRequest extends FormRequest
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
            'blocked_date' => ['required', 'date', 'unique:blocked_dates,blocked_date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
