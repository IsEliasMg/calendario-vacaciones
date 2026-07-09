<?php

declare(strict_types=1);

namespace App\Support;

use App\Domain\Settings\Models\BlockedDate;
use App\Domain\Settings\Models\Holiday;
use App\Domain\Settings\Models\Setting;
use App\Domain\Vacation\Models\Vacation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalendarAvailability
{
    /**
     * @return array<string, mixed>
     */
    public static function buildConfig(): array
    {
        $settings = Setting::current();

        return [
            'maxPeoplePerDay' => $settings->max_people_per_day,
            'maxDaysPerEmployee' => $settings->max_days_per_employee,
            'allowSaturdays' => $settings->allow_saturdays,
            'allowSundays' => $settings->allow_sundays,
            'availableYears' => $settings->available_years,
            'holidays' => Holiday::query()->pluck('holiday_date')->map(fn ($d) => Carbon::parse($d)->toDateString())->all(),
            'blockedDates' => BlockedDate::query()->pluck('blocked_date')->map(fn ($d) => Carbon::parse($d)->toDateString())->all(),
        ];
    }

    /**
     * @return Collection<string, int>
     */
    public static function occupancyByDate(?string $start = null, ?string $end = null): Collection
    {
        $query = Vacation::query()
            ->selectRaw('vacation_date, COUNT(*) as total')
            ->groupBy('vacation_date');

        if ($start) {
            $query->whereDate('vacation_date', '>=', $start);
        }

        if ($end) {
            $query->whereDate('vacation_date', '<=', $end);
        }

        return $query->pluck('total', 'vacation_date');
    }

    public static function isDateFull(string $date, ?int $maxPeople = null): bool
    {
        $maxPeople ??= Setting::current()->max_people_per_day;
        $count = Vacation::query()->whereDate('vacation_date', $date)->count();

        return $count >= $maxPeople;
    }
}
