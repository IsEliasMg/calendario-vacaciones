<?php

declare(strict_types=1);

namespace App\Domain\Settings\Actions;

use App\Domain\Settings\Models\Holiday;

class DeleteHolidayAction
{
    public function execute(Holiday $holiday): void
    {
        $holiday->delete();
    }
}
