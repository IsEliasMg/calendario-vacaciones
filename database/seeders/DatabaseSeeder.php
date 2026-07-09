<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Admin\Models\Admin;
use App\Domain\Settings\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin y settings siempre (idempotentes)
        $this->call([
            AdminSeeder::class,
            SettingsSeeder::class,
        ]);

        // Empleados demo solo la primera vez
        if (Setting::query()->exists() && \App\Domain\Employee\Models\Employee::query()->count() === 0) {
            $this->call([
                EmployeeSeeder::class,
            ]);
        }
    }
}
