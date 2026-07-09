<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Employee\Models\Employee;
use App\Domain\Vacation\Models\Vacation;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        $totalEmployees = Employee::query()->count();
        $scheduledVacations = Vacation::query()->whereDate('vacation_date', '>=', $today)->count();
        $monthVacations = Vacation::query()
            ->whereBetween('vacation_date', [$monthStart, $monthEnd])
            ->count();

        $upcomingVacations = Vacation::query()
            ->with('employee')
            ->whereDate('vacation_date', '>=', $today)
            ->orderBy('vacation_date')
            ->limit(10)
            ->get();

        $occupiedDays = Vacation::query()
            ->selectRaw('vacation_date, COUNT(*) as total')
            ->groupBy('vacation_date')
            ->having('total', '>', 0)
            ->count();

        return view('admin.dashboard.index', compact(
            'totalEmployees',
            'scheduledVacations',
            'monthVacations',
            'upcomingVacations',
            'occupiedDays',
        ));
    }
}
