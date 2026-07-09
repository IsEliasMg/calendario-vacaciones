<?php

declare(strict_types=1);

namespace App\Domain\Settings\Actions;

use App\Domain\Settings\Models\BlockedDate;

class DeleteBlockedDateAction
{
    public function execute(BlockedDate $blockedDate): void
    {
        $blockedDate->delete();
    }
}
