<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Settings\Actions\CreateBlockedDateAction;
use App\Domain\Settings\Actions\CreateHolidayAction;
use App\Domain\Settings\Actions\DeleteBlockedDateAction;
use App\Domain\Settings\Actions\DeleteHolidayAction;
use App\Domain\Settings\Actions\UpdateSettingsAction;
use App\Domain\Settings\DTOs\SettingsData;
use App\Domain\Settings\Models\BlockedDate;
use App\Domain\Settings\Models\Holiday;
use App\Domain\Settings\Models\Setting;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlockedDateRequest;
use App\Http\Requests\Admin\StoreHolidayRequest;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'settings' => Setting::current(),
            'holidays' => Holiday::query()->orderBy('holiday_date')->get(),
            'blockedDates' => BlockedDate::query()->orderBy('blocked_date')->get(),
        ]);
    }

    public function update(UpdateSettingsRequest $request, UpdateSettingsAction $action): RedirectResponse
    {
        $action->execute(Setting::current(), new SettingsData(
            maxPeoplePerDay: (int) $request->validated('max_people_per_day'),
            maxDaysPerEmployee: (int) $request->validated('max_days_per_employee'),
            allowSaturdays: $request->boolean('allow_saturdays'),
            allowSundays: $request->boolean('allow_sundays'),
            availableYears: array_map('intval', $request->validated('available_years')),
        ));

        return back()->with('success', 'Configuración actualizada correctamente.');
    }

    public function storeHoliday(StoreHolidayRequest $request, CreateHolidayAction $action): JsonResponse
    {
        $holiday = $action->execute(
            $request->validated('holiday_date'),
            $request->validated('name'),
        );

        return response()->json([
            'message' => 'Día festivo agregado.',
            'holiday' => $holiday,
        ]);
    }

    public function destroyHoliday(Holiday $holiday, DeleteHolidayAction $action): JsonResponse
    {
        $action->execute($holiday);

        return response()->json(['message' => 'Día festivo eliminado.']);
    }

    public function storeBlockedDate(StoreBlockedDateRequest $request, CreateBlockedDateAction $action): JsonResponse
    {
        $blockedDate = $action->execute(
            $request->validated('blocked_date'),
            $request->validated('reason'),
        );

        return response()->json([
            'message' => 'Fecha bloqueada agregada.',
            'blockedDate' => $blockedDate,
        ]);
    }

    public function destroyBlockedDate(BlockedDate $blockedDate, DeleteBlockedDateAction $action): JsonResponse
    {
        $action->execute($blockedDate);

        return response()->json(['message' => 'Fecha bloqueada eliminada.']);
    }
}
