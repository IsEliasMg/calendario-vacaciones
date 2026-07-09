<?php

declare(strict_types=1);

namespace App\Domain\Vacation\Actions;

use App\Domain\Vacation\Models\Vacation;

class DeleteVacationAction
{
    public function execute(Vacation $vacation): void
    {
        $vacation->delete();
    }
}
