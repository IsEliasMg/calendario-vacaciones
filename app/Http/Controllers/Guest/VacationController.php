<?php

declare(strict_types=1);

namespace App\Http\Controllers\Guest;

use App\Domain\Employee\Actions\UpdateEmployeeColorAction;
use App\Domain\Employee\Models\Employee;
use App\Domain\Vacation\Actions\RegisterVacationAction;
use App\Domain\Vacation\DTOs\VacationData;
use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guest\RegisterVacationRequest;
use App\Http\Requests\Guest\UpdateColorRequest;
use App\Support\CalendarAvailability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VacationController extends Controller
{
    public function create(): View
    {
        /** @var Employee $employee */
        $employee = request()->attributes->get('employee');

        $existingDates = $employee->vacations()
            ->whereDate('vacation_date', '>=', now()->toDateString())
            ->orderBy('vacation_date')
            ->pluck('vacation_date')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->values()
            ->all();

        return view('guest.calendar', [
            'employee' => $employee,
            'calendarConfig' => CalendarAvailability::buildConfig(),
            'existingDates' => $existingDates,
        ]);
    }

    public function store(RegisterVacationRequest $request, RegisterVacationAction $action): RedirectResponse
    {
        $dates = $request->input('dates', []);

        if (! is_array($dates)) {
            $dates = [];
        }

        try {
            $action->execute(new VacationData(
                employeeId: (int) session('employee_id'),
                dates: array_values($dates),
            ));
        } catch (DomainException $exception) {
            return back()->withErrors(['dates' => $exception->getMessage()]);
        }

        session()->forget('employee_id');

        return redirect()->route('guest.vacations.thanks');
    }

    public function updateColor(
        UpdateColorRequest $request,
        UpdateEmployeeColorAction $action,
    ): JsonResponse {
        /** @var Employee $employee */
        $employee = request()->attributes->get('employee');

        $employee = $action->execute($employee, $request->validated('color'));

        return response()->json([
            'message' => 'Color actualizado.',
            'color' => $employee->color,
        ]);
    }

    public function thanks(): View
    {
        return view('guest.thanks');
    }
}
