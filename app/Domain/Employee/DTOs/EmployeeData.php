<?php

declare(strict_types=1);

namespace App\Domain\Employee\DTOs;

readonly class EmployeeData
{
    public function __construct(
        public string $name,
        public ?string $color = null,
    ) {
    }
}
