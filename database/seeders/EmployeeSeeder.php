<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Employee\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            'María González',
            'Juan Pérez',
            'Pedro Ramírez',
            'Ana López',
            'Carlos Martínez',
        ];

        foreach ($employees as $name) {
            // firstOrCreate: no sobrescribe color ni datos de empleados ya existentes
            Employee::query()->firstOrCreate(
                ['name' => $name],
                [
                    'color' => Employee::DEFAULT_COLOR,
                    'status' => Employee::STATUS_ACTIVE,
                    'token' => Str::random(32),
                ]
            );
        }
    }
}
