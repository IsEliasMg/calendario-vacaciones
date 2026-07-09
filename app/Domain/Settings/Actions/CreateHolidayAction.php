<?php

declare(strict_types=1);

namespace App\Domain\Settings\Actions;

use App\Domain\Settings\Models\Holiday;
use Carbon\Carbon;

class CreateHolidayAction
{
    public function execute(string $date, string $name): Holiday
    {
        return Holiday::query()->create([
            'holiday_date' => Carbon::parse($date)->toDateString(),
            'name' => $name,
        ]);
    }
}
