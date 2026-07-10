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
            ->with(['employee' => fn ($query) => $query->withTrashed()])
            ->whereDate('vacation_date', '>=', $today)
            ->orderBy('vacation_date')
            ->limit(30)
            ->get()
            ->filter(fn (Vacation $vacation) => $vacation->employee !== null)
            ->take(10)
            ->values();

        $occupiedDays = Vacation::query()
            ->selectRaw('vacation_date, COUNT(*) as total')
            ->groupBy('vacation_date')
            ->havingRaw('COUNT(*) > 0')
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
