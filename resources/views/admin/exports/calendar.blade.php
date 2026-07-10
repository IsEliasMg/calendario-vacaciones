<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Vacaciones — {{ $monthLabel }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
        }
        h1 {
            font-size: 18px;
            color: #6B1D2A;
            margin: 0 0 2px 0;
        }
        .subtitle {
            color: #6b7280;
            margin-bottom: 10px;
            font-size: 11px;
        }
        table.calendar {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.calendar th {
            background: #6B1D2A !important;
            color: #fff !important;
            font-size: 10px;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #4a1420;
        }
        table.calendar td {
            border: 1px solid #d1d5db;
            vertical-align: top;
            height: 78px;
            padding: 4px;
            width: 14.28%;
            background: #fff;
        }
        td.out-month {
            background: #f3f4f6 !important;
            color: #9ca3af;
        }
        .day-number {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 3px;
            text-align: right;
            color: #374151;
        }
        td.out-month .day-number { color: #9ca3af; }
        .tag {
            display: block;
            color: #fff !important;
            border-radius: 3px;
            padding: 2px 3px;
            font-size: 7.5px;
            margin-bottom: 2px;
            font-weight: bold;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .legend {
            margin-top: 10px;
            font-size: 9px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 4px;
        }
        .legend-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 2px;
            margin-right: 3px;
            vertical-align: middle;
        }
        .no-print { margin-bottom: 12px; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
    </style>
</head>
<body @if (! empty($autoPrint)) onload="window.print()" @endif>
    @if (! empty($autoPrint))
        <div class="no-print">
            <button type="button" onclick="window.print()">Imprimir</button>
        </div>
    @endif

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
                        <td class="{{ ! $day['inMonth'] ? 'out-month' : '' }}">
                            <div class="day-number">{{ $day['date']->day }}</div>
                            @if ($day['inMonth'])
                                @foreach ($day['employees'] as $employee)
                                    <span class="tag" style="background-color: {{ $employee['color'] }} !important;">
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
                    <span class="legend-dot" style="background-color: {{ $item['color'] }} !important;"></span>
                    {{ $item['name'] }}
                </span>
            @endforeach
        </div>
    @endif
</body>
</html>
