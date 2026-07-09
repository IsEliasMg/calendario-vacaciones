<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Admin\Models\Admin;
use App\Domain\Employee\Models\Employee;

class EmployeePolicy
{
    public function viewAny(Admin $admin): bool
    {
        return true;
    }

    public function create(Admin $admin): bool
    {
        return true;
    }

    public function update(Admin $admin, Employee $employee): bool
    {
        return true;
    }

    public function delete(Admin $admin, Employee $employee): bool
    {
        return true;
    }
}
