<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Vacaciones</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 24px; }
        h1 { font-size: 22px; margin-bottom: 4px; color: #1a73e8; }
        .subtitle { color: #6b7280; margin-bottom: 20px; }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }
        .day-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            min-height: 110px;
            padding: 8px;
            page-break-inside: avoid;
        }
        .day-number {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 6px;
        }
        .tag {
            display: inline-block;
            color: #fff;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            margin: 2px 2px 0 0;
            font-weight: 600;
        }
        .empty { color: #9ca3af; font-size: 12px; }
        .list-section { margin-top: 28px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 12px; }
        th { background: #f3f4f6; }
        .color-dot { display: inline-block; width: 12px; height: 12px; border-radius: 3px; margin-right: 6px; vertical-align: middle; }
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 16px;">
        <button onclick="window.print()">Imprimir</button>
    </div>

    <h1>Calendario de Vacaciones</h1>
    <div class="subtitle">{{ $monthLabel }} — {{ $filters }}</div>

    @if ($days->isEmpty())
        <p class="empty">No hay vacaciones para los filtros seleccionados.</p>
    @else
        <div class="calendar-grid">
            @foreach ($days as $day)
                <div class="day-card">
                    <div class="day-number">{{ $day['date']->translatedFormat('d M') }}</div>
                    @foreach ($day['employees'] as $employee)
                        <span class="tag" style="background: {{ $employee['color'] }}">{{ $employee['name'] }}</span>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="list-section">
            <h2 style="font-size: 16px;">Detalle</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Empleado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vacations as $vacation)
                        <tr>
                            <td>{{ $vacation->vacation_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="color-dot" style="background: {{ $vacation->employee->color ?? '#4285F4' }}"></span>
                                {{ $vacation->employee->name ?? 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>
