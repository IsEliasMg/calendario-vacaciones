<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\VacationController as AdminVacationController;
use App\Http\Controllers\Api\CalendarApiController;
use App\Http\Controllers\Guest\NameController;
use App\Http\Controllers\Guest\VacationController;
use App\Http\Middleware\AdminAuthenticated;
use App\Http\Middleware\EnsureEmployeeSession;
use Illuminate\Support\Facades\Route;

Route::get('/', [NameController::class, 'create'])->name('guest.name');
Route::post('/', [NameController::class, 'store'])->name('guest.name.store');

Route::middleware(EnsureEmployeeSession::class)->group(function (): void {
    Route::get('/vacaciones', [VacationController::class, 'create'])->name('guest.vacations.create');
    Route::post('/vacaciones', [VacationController::class, 'store'])->name('guest.vacations.store');
    Route::post('/vacaciones/color', [VacationController::class, 'updateColor'])->name('guest.vacations.color');
});

Route::get('/vacaciones/gracias', [VacationController::class, 'thanks'])->name('guest.vacations.thanks');

Route::prefix('api/calendar')->name('api.calendar.')->group(function (): void {
    Route::get('/events', [CalendarApiController::class, 'events'])->name('events');
    Route::get('/blocked-dates', [CalendarApiController::class, 'blockedDates'])->name('blocked-dates');
    Route::get('/legend', [CalendarApiController::class, 'legend'])->name('legend');
});

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::middleware(AdminAuthenticated::class)->group(function (): void {
        Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');

        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::post('/employees/{employee}/block', [EmployeeController::class, 'block'])->name('employees.block');
        Route::post('/employees/{employee}/reactivate', [EmployeeController::class, 'reactivate'])->name('employees.reactivate');
        Route::get('/employees/{employee}/history', [EmployeeController::class, 'history'])->name('employees.history');

        Route::delete('/vacations/{vacation}', [AdminVacationController::class, 'destroy'])->name('vacations.destroy');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/holidays', [SettingsController::class, 'storeHoliday'])->name('settings.holidays.store');
        Route::delete('/settings/holidays/{holiday}', [SettingsController::class, 'destroyHoliday'])->name('settings.holidays.destroy');
        Route::post('/settings/blocked-dates', [SettingsController::class, 'storeBlockedDate'])->name('settings.blocked-dates.store');
        Route::delete('/settings/blocked-dates/{blockedDate}', [SettingsController::class, 'destroyBlockedDate'])->name('settings.blocked-dates.destroy');

        Route::get('/exports/print', [ExportController::class, 'print'])->name('exports.print');
        Route::get('/exports/pdf', [ExportController::class, 'pdf'])->name('exports.pdf');
        Route::get('/exports/excel', [ExportController::class, 'excel'])->name('exports.excel');
    });
});
