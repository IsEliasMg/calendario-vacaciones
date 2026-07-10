<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Vacaciones — {{ $monthLabel }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1f2937;
            margin: 20px;
        }
        h1 {
            font-size: 22px;
            margin: 0 0 4px 0;
            color: #6B1D2A;
        }
        .subtitle {
            color: #6b7280;
            margin-bottom: 16px;
            font-size: 13px;
        }
        table.calendar {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.calendar th {
            background: #6B1D2A;
            color: #fff;
            font-size: 12px;
            padding: 8px 4px;
            text-align: center;
            border: 1px solid #4a1420;
        }
        table.calendar td {
            border: 1px solid #d1d5db;
            vertical-align: top;
            height: 100px;
            padding: 6px;
            width: 14.28%;
        }
        td.out-month {
            background: #f3f4f6;
            color: #9ca3af;
        }
        td.today {
            background: #fef9c3;
        }
        .day-number {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 4px;
            text-align: right;
            color: #374151;
        }
        td.out-month .day-number { color: #9ca3af; }
        .tag {
            display: block;
            color: #fff;
            border-radius: 4px;
            padding: 3px 5px;
            font-size: 10px;
            margin-bottom: 3px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .legend {
            margin-top: 16px;
            font-size: 12px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 14px;
            margin-bottom: 6px;
        }
        .legend-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            margin-right: 4px;
            vertical-align: middle;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            table.calendar td { height: 90px; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 16px;">
        <button onclick="window.print()">Imprimir</button>
    </div>

    <h1>Calendario de Vacaciones</h1>
    <div class="subtitle">{{ ucfirst($monthLabel) }} — {{ $filters }}</div>

    <table class="calendar">
        <thead>
            <tr>
                @foreach ($weekDays as $dayName)
                    <th>{{ $dayName }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($weeks as $week)
                <tr>
                    @foreach ($week as $day)
                        <td class="{{ ! $day['inMonth'] ? 'out-month' : '' }} {{ $day['isToday'] ? 'today' : '' }}">
                            <div class="day-number">{{ $day['date']->day }}</div>
                            @if ($day['inMonth'])
                                @foreach ($day['employees'] as $employee)
                                    <span class="tag" style="background: {{ $employee['color'] }}">
                                        {{ $employee['name'] }}
                                    </span>
                                @endforeach
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($legend->isNotEmpty())
        <div class="legend">
            @foreach ($legend as $item)
                <span class="legend-item">
                    <span class="legend-dot" style="background: {{ $item['color'] }}"></span>
                    {{ $item['name'] }}
                </span>
            @endforeach
        </div>
    @endif
</body>
</html>
