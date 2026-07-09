<?php

declare(strict_types=1);

namespace App\Domain\Employee\Models;

use App\Domain\Vacation\Models\Vacation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Employee extends Model
{
    use SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_BLOCKED = 'blocked';

    public const DEFAULT_COLOR = '#9CA3AF';

    protected $fillable = [
        'name',
        'color',
        'status',
        'token',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Employee $employee): void {
            if (empty($employee->token)) {
                $employee->token = Str::random(32);
            }

            if (empty($employee->color)) {
                $employee->color = self::DEFAULT_COLOR;
            }
        });
    }

    public function vacations(): HasMany
    {
        return $this->hasMany(Vacation::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }
}
