<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Employee\Models\Employee;
use App\Domain\Vacation\Models\Vacation;
use App\Policies\EmployeePolicy;
use App\Policies\VacationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(Vacation::class, VacationPolicy::class);
    }
}
