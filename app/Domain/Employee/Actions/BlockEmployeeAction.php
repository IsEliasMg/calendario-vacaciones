<?php

declare(strict_types=1);

namespace App\Domain\Employee\Actions;

use App\Domain\Employee\Models\Employee;

class BlockEmployeeAction
{
    public function execute(Employee $employee): Employee
    {
        $employee->update(['status' => Employee::STATUS_BLOCKED]);

        return $employee->fresh();
    }
}
