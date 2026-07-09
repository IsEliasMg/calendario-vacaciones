<?php

declare(strict_types=1);

namespace App\Domain\Settings\Actions;

use App\Domain\Settings\Models\BlockedDate;
use Carbon\Carbon;

class CreateBlockedDateAction
{
    public function execute(string $date, ?string $reason = null): BlockedDate
    {
        return BlockedDate::query()->create([
            'blocked_date' => Carbon::parse($date)->toDateString(),
            'reason' => $reason,
        ]);
    }
}
