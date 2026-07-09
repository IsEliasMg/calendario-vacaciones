<?php

declare(strict_types=1);

namespace App\Domain\Vacation\Actions;

use App\Domain\Employee\Models\Employee;
use App\Domain\Settings\Models\BlockedDate;
use App\Domain\Settings\Models\Holiday;
use App\Domain\Settings\Models\Setting;
use App\Domain\Vacation\DTOs\VacationData;
use App\Domain\Vacation\Models\Vacation;
use App\Exceptions\DomainException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegisterVacationAction
{
    public function execute(VacationData $data): int
    {
        $employee = Employee::query()->findOrFail($data->employeeId);

        if ($employee->isBlocked()) {
            throw new DomainException('El empleado está bloqueado y no puede registrar vacaciones.');
        }

        $settings = Setting::current();
        $selectedDates = collect($data->dates)
            ->filter(fn ($date): bool => is_string($date) && $date !== '')
            ->map(fn (string $date): string => Carbon::parse($date)->toDateString())
            ->unique()
            ->sort()
            ->values();

        if ($selectedDates->count() > $settings->max_days_per_employee) {
            throw new DomainException("No puedes registrar más de {$settings->max_days_per_employee} días de vacaciones.");
        }

        foreach ($selectedDates as $dateString) {
            $this->assertDateIsAllowed($dateString, $settings);
        }

        $today = Carbon::today()->toDateString();

        return DB::transaction(function () use ($employee, $settings, $selectedDates, $today): int {
            $employee->vacations()
                ->whereDate('vacation_date', '>=', $today)
                ->delete();

            foreach ($selectedDates as $dateString) {
                $count = Vacation::query()
                    ->whereDate('vacation_date', $dateString)
                    ->lockForUpdate()
                    ->count();

                if ($count >= $settings->max_people_per_day) {
                    throw new DomainException('Cupo lleno para el día '.Carbon::parse($dateString)->format('d/m/Y').'.');
                }

                Vacation::query()->create([
                    'employee_id' => $employee->id,
                    'vacation_date' => $dateString,
                ]);
            }

            return $selectedDates->count();
        });
    }

    private function assertDateIsAllowed(string $dateString, Setting $settings): void
    {
        $date = Carbon::parse($dateString);
        $today = Carbon::today();

        if ($date->lt($today)) {
            throw new DomainException("No puedes registrar vacaciones en fechas pasadas: {$date->format('d/m/Y')}.");
        }

        if (! $settings->allow_saturdays && $date->isSaturday()) {
            throw new DomainException("Los sábados no están permitidos: {$date->format('d/m/Y')}.");
        }

        if (! $settings->allow_sundays && $date->isSunday()) {
            throw new DomainException("Los domingos no están permitidos: {$date->format('d/m/Y')}.");
        }

        if (! in_array((int) $date->year, $settings->available_years, true)) {
            throw new DomainException("El año {$date->year} no está disponible para registrar vacaciones.");
        }

        if (Holiday::query()->whereDate('holiday_date', $date)->exists()) {
            throw new DomainException("{$date->format('d/m/Y')} es un día festivo.");
        }

        if (BlockedDate::query()->whereDate('blocked_date', $date)->exists()) {
            throw new DomainException("{$date->format('d/m/Y')} está bloqueado.");
        }
    }
}
