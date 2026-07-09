<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Admin\Models\Admin;
use App\Domain\Vacation\Models\Vacation;

class VacationPolicy
{
    public function delete(Admin $admin, Vacation $vacation): bool
    {
        return true;
    }
}
