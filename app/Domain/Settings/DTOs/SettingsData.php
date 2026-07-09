<?php

declare(strict_types=1);

namespace App\Domain\Settings\DTOs;

readonly class SettingsData
{
    /**
     * @param  list<int>  $availableYears
     */
    public function __construct(
        public int $maxPeoplePerDay,
        public int $maxDaysPerEmployee,
        public bool $allowSaturdays,
        public bool $allowSundays,
        public array $availableYears,
    ) {
    }
}
