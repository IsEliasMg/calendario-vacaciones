<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Settings\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        if (Setting::query()->exists()) {
            return;
        }

        $currentYear = (int) date('Y');

        Setting::query()->create([
            'max_people_per_day' => 3,
            'max_days_per_employee' => 15,
            'allow_saturdays' => false,
            'allow_sundays' => false,
            'available_years' => [$currentYear, $currentYear + 1],
        ]);
    }
}
