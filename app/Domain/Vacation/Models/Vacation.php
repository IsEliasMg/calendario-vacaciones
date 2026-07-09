<?php

declare(strict_types=1);

namespace App\Domain\Vacation\Models;

use App\Domain\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacation extends Model
{
    protected $fillable = [
        'employee_id',
        'vacation_date',
    ];

    protected function casts(): array
    {
        return [
            'vacation_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
