<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Employee\Models\Employee;
use App\Http\Controllers\Controller;
use App\Support\CalendarAvailability;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        return view('admin.calendar.index', [
            'employees' => Employee::query()->orderBy('name')->get(['id', 'name', 'color']),
            'calendarConfig' => CalendarAvailability::buildConfig(),
        ]);
    }
}
