<?php

declare(strict_types=1);

namespace App\Domain\Vacation\DTOs;

readonly class VacationData
{
    /**
     * @param  list<string>  $dates
     */
    public function __construct(
        public int $employeeId,
        public array $dates,
    ) {
    }
}
