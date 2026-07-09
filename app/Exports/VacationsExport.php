<?php

declare(strict_types=1);

namespace App\Exports;

use App\Domain\Vacation\Models\Vacation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VacationsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @param  Collection<int, Vacation>  $vacations
     */
    public function __construct(private readonly Collection $vacations)
    {
    }

    public function collection(): Collection
    {
        return $this->vacations;
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return ['Empleado', 'Fecha', 'Registrado el'];
    }

    /**
     * @param  Vacation  $vacation
     * @return list<string>
     */
    public function map($vacation): array
    {
        return [
            $vacation->employee?->name ?? 'N/A',
            $vacation->vacation_date->format('d/m/Y'),
            $vacation->created_at?->format('d/m/Y H:i') ?? '',
        ];
    }
}
