<?php

declare(strict_types=1);

namespace App\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    private const CACHE_KEY = 'app.settings';

    protected $fillable = [
        'max_people_per_day',
        'max_days_per_employee',
        'allow_saturdays',
        'allow_sundays',
        'available_years',
    ];

    protected function casts(): array
    {
        return [
            'max_people_per_day' => 'integer',
            'max_days_per_employee' => 'integer',
            'allow_saturdays' => 'boolean',
            'allow_sundays' => 'boolean',
            'available_years' => 'array',
        ];
    }

    public static function current(): self
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): self {
            return self::query()->firstOrFail();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            self::clearCache();
        });
        static::deleted(function (): void {
            self::clearCache();
        });
    }
}
