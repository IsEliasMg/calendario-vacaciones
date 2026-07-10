<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Employee\Models\Employee;
use App\Domain\Vacation\Models\Vacation;
use App\Exports\VacationsExport;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function print(Request $request): View
    {
        return view('admin.exports.print', $this->exportPayload($request));
    }

    public function pdf(Request $request): Response
    {
        $payload = $this->exportPayload($request);

        $pdf = Pdf::loadView('admin.exports.pdf', $payload)
            ->setPaper('a4', 'landscape');

        return $pdf->download('calendario-vacaciones.pdf');
    }

    public function excel(Request $request): BinaryFileResponse
    {
        $vacations = $this->filteredVacations($request);

        return Excel::download(
            new VacationsExport($vacations),
            'calendario-vacaciones.xlsx'
        );
    }

    /**
     * @return array{
     *     weeks: array<int, array<int, array{date: Carbon, inMonth: bool, employees: Collection}>>,
     *     weekDays: array<int, string>,
     *     filters: string,
     *     monthLabel: string,
     *     legend: Collection
     * }
     */
    private function exportPayload(Request $request): array
    {
        $monthStart = $this->resolveMonthStart($request);
        $monthEnd = $monthStart->copy()->endOfMonth();

        $vacations = $this->filteredVacations($request, $monthStart, $monthEnd);

        $byDate = $vacations
            ->groupBy(fn (Vacation $vacation) => $vacation->vacation_date->format('Y-m-d'))
            ->map(function (Collection $items) {
                return $items
                    ->map(fn (Vacation $vacation) => [
                        'name' => $vacation->employee?->name ?? 'N/A',
                        'color' => $vacation->employee?->color ?? '#9CA3AF',
                    ])
                    ->unique('name')
                    ->values();
            });

        $legend = $vacations
            ->map(fn (Vacation $vacation) => [
                'name' => $vacation->employee?->name ?? 'N/A',
                'color' => $vacation->employee?->color ?? '#9CA3AF',
            ])
            ->unique('name')
            ->sortBy('name')
            ->values();

        return [
            'weeks' => $this->buildMonthWeeks($monthStart, $byDate),
            'weekDays' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            'filters' => $this->filtersLabel($request),
            'monthLabel' => $monthStart->translatedFormat('F Y'),
            'legend' => $legend,
        ];
    }

    private function resolveMonthStart(Request $request): Carbon
    {
        $year = $request->integer('year') ?: (int) date('Y');
        $month = $request->integer('month') ?: (int) date('n');

        return Carbon::create($year, $month, 1)->startOfDay();
    }

    /**
     * @param  Collection<string, Collection>  $byDate
     * @return array<int, array<int, array{date: Carbon, inMonth: bool, employees: Collection}>>
     */
    private function buildMonthWeeks(Carbon $monthStart, Collection $byDate): array
    {
        $cursor = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $end = $monthStart->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        $weeks = [];
        $week = [];

        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();

            $week[] = [
                'date' => $cursor->copy(),
                'inMonth' => $cursor->month === $monthStart->month,
                'employees' => $byDate->get($key, collect()),
            ];

            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }

            $cursor->addDay();
        }

        return $weeks;
    }

    /**
     * @return Collection<int, Vacation>
     */
    private function filteredVacations(Request $request, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $employeeId = $request->integer('employee_id');
        $month = $request->integer('month');
        $year = $request->integer('year');
        $search = $request->string('search')->trim()->toString();

        return Vacation::query()
            ->with('employee')
            ->when($from && $to, fn ($q) => $q->whereBetween('vacation_date', [$from->toDateString(), $to->toDateString()]))
            ->when(! $from && $month > 0, fn ($q) => $q->whereMonth('vacation_date', $month))
            ->when(! $from && $year > 0, fn ($q) => $q->whereYear('vacation_date', $year))
            ->when($employeeId > 0, fn ($q) => $q->where('employee_id', $employeeId))
            ->when($search !== '', function ($q) use ($search): void {
                $q->whereHas('employee', fn ($employeeQuery) => $employeeQuery->where('name', 'like', "%{$search}%"));
            })
            ->orderBy('vacation_date')
            ->get();
    }

    private function filtersLabel(Request $request): string
    {
        $parts = [];

        if ($request->filled('employee_id')) {
            $employee = Employee::query()->find($request->integer('employee_id'));
            if ($employee) {
                $parts[] = 'Empleado: '.$employee->name;
            }
        }

        if ($request->filled('search')) {
            $parts[] = 'Búsqueda: '.$request->string('search');
        }

        return $parts === [] ? 'Todas las vacaciones del mes' : implode(' | ', $parts);
    }
}
