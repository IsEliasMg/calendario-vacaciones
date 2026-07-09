<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Vacaciones</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 18px; color: #1a73e8; margin-bottom: 4px; }
        .subtitle { color: #6b7280; margin-bottom: 16px; }
        table.calendar { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.calendar td {
            border: 1px solid #d1d5db;
            vertical-align: top;
            height: 90px;
            padding: 6px;
            width: 14.28%;
        }
        .day-number { font-weight: bold; margin-bottom: 4px; font-size: 11px; }
        .tag {
            display: block;
            color: #fff;
            border-radius: 4px;
            padding: 2px 4px;
            font-size: 9px;
            margin-bottom: 3px;
            font-weight: bold;
        }
        .detail { margin-top: 18px; width: 100%; border-collapse: collapse; }
        .detail th, .detail td { border: 1px solid #ccc; padding: 5px; }
        .detail th { background: #e8f0fe; }
        .dot { display: inline-block; width: 8px; height: 8px; border-radius: 2px; margin-right: 4px; }
    </style>
</head>
<body>
    <h1>Calendario de Vacaciones</h1>
    <div class="subtitle">{{ $monthLabel }} — {{ $filters }}</div>

    @if ($days->isEmpty())
        <p>No hay vacaciones para los filtros seleccionados.</p>
    @else
        <table class="calendar">
            @foreach ($days->chunk(7) as $row)
                <tr>
                    @foreach ($row as $day)
                        <td>
                            <div class="day-number">{{ $day['date']->format('d/m/Y') }}</div>
                            @foreach ($day['employees'] as $employee)
                                <span class="tag" style="background-color: {{ $employee['color'] }};">{{ $employee['name'] }}</span>
                            @endforeach
                        </td>
                    @endforeach
                    @for ($i = $row->count(); $i < 7; $i++)
                        <td></td>
                    @endfor
                </tr>
            @endforeach
        </table>

        <table class="detail">
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
                            <span class="dot" style="background-color: {{ $vacation->employee->color ?? '#4285F4' }};"></span>
                            {{ $vacation->employee->name ?? 'N/A' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
