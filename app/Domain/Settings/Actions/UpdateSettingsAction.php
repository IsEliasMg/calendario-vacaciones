<?php

declare(strict_types=1);

namespace App\Domain\Settings\Actions;

use App\Domain\Settings\DTOs\SettingsData;
use App\Domain\Settings\Models\Setting;

class UpdateSettingsAction
{
    public function execute(Setting $setting, SettingsData $data): Setting
    {
        $setting->update([
            'max_people_per_day' => $data->maxPeoplePerDay,
            'max_days_per_employee' => $data->maxDaysPerEmployee,
            'allow_saturdays' => $data->allowSaturdays,
            'allow_sundays' => $data->allowSundays,
            'available_years' => $data->availableYears,
        ]);

        return $setting->fresh();
    }
}
