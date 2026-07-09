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
     * @return array{vacations: Collection, days: Collection, filters: string, monthLabel: string}
     */
    private function exportPayload(Request $request): array
    {
        $vacations = $this->filteredVacations($request);

        $days = $vacations
            ->groupBy(fn (Vacation $vacation) => $vacation->vacation_date->format('Y-m-d'))
            ->sortKeys()
            ->map(function (Collection $items, string $date) {
                return [
                    'date' => Carbon::parse($date),
                    'employees' => $items->map(fn (Vacation $vacation) => [
                        'name' => $vacation->employee?->name ?? 'N/A',
                        'color' => $vacation->employee?->color ?? '#4285F4',
                    ])->unique('name')->values(),
                ];
            })
            ->values();

        return [
            'vacations' => $vacations,
            'days' => $days,
            'filters' => $this->filtersLabel($request),
            'monthLabel' => $this->monthLabel($request),
        ];
    }

    /**
     * @return Collection<int, Vacation>
     */
    private function filteredVacations(Request $request): Collection
    {
        $employeeId = $request->integer('employee_id');
        $month = $request->integer('month');
        $year = $request->integer('year');
        $search = $request->string('search')->trim()->toString();

        return Vacation::query()
            ->with('employee')
            ->when($employeeId > 0, fn ($q) => $q->where('employee_id', $employeeId))
            ->when($month > 0, fn ($q) => $q->whereMonth('vacation_date', $month))
            ->when($year > 0, fn ($q) => $q->whereYear('vacation_date', $year))
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

        if ($request->filled('month')) {
            $parts[] = 'Mes: '.$request->integer('month');
        }

        if ($request->filled('year')) {
            $parts[] = 'Año: '.$request->integer('year');
        }

        if ($request->filled('search')) {
            $parts[] = 'Búsqueda: '.$request->string('search');
        }

        return $parts === [] ? 'Todos los registros' : implode(' | ', $parts);
    }

    private function monthLabel(Request $request): string
    {
        $year = $request->integer('year') ?: (int) date('Y');
        $month = $request->integer('month');

        if ($month > 0) {
            return Carbon::create($year, $month, 1)->translatedFormat('F Y');
        }

        return (string) $year;
    }
}
