@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-semibold mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Resumen general de vacaciones</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-people"></i></div>
                <div>
                    <div class="text-muted small">Empleados registrados</div>
                    <div class="fs-3 fw-bold">{{ $totalEmployees }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <div class="text-muted small">Vacaciones programadas</div>
                    <div class="fs-3 fw-bold">{{ $scheduledVacations }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-calendar-month"></i></div>
                <div>
                    <div class="text-muted small">Vacaciones del mes</div>
                    <div class="fs-3 fw-bold">{{ $monthVacations }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-calendar-day"></i></div>
                <div>
                    <div class="text-muted small">Días ocupados</div>
                    <div class="fs-3 fw-bold">{{ $occupiedDays }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card-soft p-4">
    <h2 class="h5 fw-semibold mb-3">Próximas vacaciones</h2>
    @if ($upcomingVacations->isEmpty())
        <p class="text-muted mb-0">No hay vacaciones próximas registradas.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Fecha</th>
                        <th>Registrado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($upcomingVacations as $vacation)
                        <tr>
                            <td>
                                <span class="legend-color" style="background: {{ $vacation->employee->color }}"></span>
                                {{ $vacation->employee->name }}
                            </td>
                            <td>{{ $vacation->vacation_date->format('d/m/Y') }}</td>
                            <td class="text-muted">{{ $vacation->created_at->timezone('America/Mexico_City')->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
