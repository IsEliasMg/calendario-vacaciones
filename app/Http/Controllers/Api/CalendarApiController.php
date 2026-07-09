<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Employee\Models\Employee;
use App\Domain\Settings\Models\BlockedDate;
use App\Domain\Settings\Models\Holiday;
use App\Domain\Settings\Models\Setting;
use App\Domain\Vacation\Models\Vacation;
use App\Http\Controllers\Controller;
use App\Support\CalendarAvailability;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarApiController extends Controller
{
    public function events(Request $request): JsonResponse
    {
        $start = $this->normalizeDate($request->string('start')->toString());
        $end = $this->normalizeDate($request->string('end')->toString());
        $employeeId = $request->integer('employee_id');
        $search = $request->string('search')->trim()->toString();
        $since = $request->string('since')->toString();
        $month = $request->integer('month');
        $year = $request->integer('year');

        $query = Vacation::query()
            ->with('employee:id,name,color,status')
            ->when($employeeId > 0, fn ($q) => $q->where('employee_id', $employeeId))
            ->when($search !== '', function ($q) use ($search): void {
                $q->whereHas('employee', fn ($employeeQuery) => $employeeQuery->where('name', 'like', "%{$search}%"));
            })
            ->when($month > 0, fn ($q) => $q->whereMonth('vacation_date', $month))
            ->when($year > 0, fn ($q) => $q->whereYear('vacation_date', $year))
            ->when($start !== '', fn ($q) => $q->whereDate('vacation_date', '>=', $start))
            ->when($end !== '', fn ($q) => $q->whereDate('vacation_date', '<=', $end))
            ->when($since !== '', fn ($q) => $q->where('updated_at', '>=', Carbon::parse($since)));

        $events = $query->orderBy('vacation_date')->get()->map(function (Vacation $vacation): array {
            $employee = $vacation->employee;
            $color = $employee?->color ?: '#9CA3AF';

            return [
                'id' => $vacation->id,
                'title' => $employee?->name ?? 'Empleado',
                'start' => $vacation->vacation_date->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'employeeId' => $vacation->employee_id,
                    'employeeName' => $employee?->name,
                    'employeeColor' => $color,
                ],
            ];
        });

        return response()->json([
            'events' => $events,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function blockedDates(Request $request): JsonResponse
    {
        $settings = Setting::current();
        $start = $this->normalizeDate($request->string('start')->toString());
        $end = $this->normalizeDate($request->string('end')->toString());

        $occupancy = CalendarAvailability::occupancyByDate(
            $start !== '' ? $start : null,
            $end !== '' ? $end : null,
        );

        $holidays = Holiday::query()
            ->when($start !== '', fn ($q) => $q->whereDate('holiday_date', '>=', $start))
            ->when($end !== '', fn ($q) => $q->whereDate('holiday_date', '<=', $end))
            ->get()
            ->map(fn (Holiday $holiday): array => [
                'date' => $holiday->holiday_date->format('Y-m-d'),
                'type' => 'holiday',
                'label' => $holiday->name,
            ]);

        $blocked = BlockedDate::query()
            ->when($start !== '', fn ($q) => $q->whereDate('blocked_date', '>=', $start))
            ->when($end !== '', fn ($q) => $q->whereDate('blocked_date', '<=', $end))
            ->get()
            ->map(fn (BlockedDate $blockedDate): array => [
                'date' => $blockedDate->blocked_date->format('Y-m-d'),
                'type' => 'blocked',
                'label' => $blockedDate->reason ?? 'Bloqueado',
            ]);

        $fullDays = $occupancy
            ->filter(fn (int $count) => $count >= $settings->max_people_per_day)
            ->keys()
            ->map(fn (string $date): array => [
                'date' => Carbon::parse($date)->format('Y-m-d'),
                'type' => 'full',
                'label' => 'Cupo lleno',
            ]);

        return response()->json([
            'config' => CalendarAvailability::buildConfig(),
            'holidays' => $holidays->values(),
            'blocked' => $blocked->values(),
            'fullDays' => $fullDays->values(),
            'occupancy' => $occupancy->mapWithKeys(fn (int $count, string $date) => [
                Carbon::parse($date)->format('Y-m-d') => $count,
            ]),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function legend(): JsonResponse
    {
        $employees = Employee::query()
            ->orderBy('name')
            ->get(['id', 'name', 'color', 'status']);

        return response()->json(['employees' => $employees]);
    }

    private function normalizeDate(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return Carbon::parse($value)->toDateString();
    }
}
