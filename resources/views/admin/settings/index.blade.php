@extends('layouts.admin')

@section('title', 'Configuración')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-semibold mb-1">Configuración</h1>
    <p class="text-muted mb-0">Define las reglas del calendario de vacaciones</p>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-soft p-4">
            <h2 class="h5 fw-semibold mb-3">Reglas generales</h2>
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Máximo personas por día</label>
                        <input type="number" name="max_people_per_day" class="form-control" min="1" max="50"
                            value="{{ old('max_people_per_day', $settings->max_people_per_day) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Máximo días por empleado</label>
                        <input type="number" name="max_days_per_employee" class="form-control" min="1" max="365"
                            value="{{ old('max_days_per_employee', $settings->max_days_per_employee) }}" required>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="allow_saturdays" name="allow_saturdays" value="1"
                                @checked(old('allow_saturdays', $settings->allow_saturdays))>
                            <label class="form-check-label" for="allow_saturdays">Permitir seleccionar sábados</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="allow_sundays" name="allow_sundays" value="1"
                                @checked(old('allow_sundays', $settings->allow_sundays))>
                            <label class="form-check-label" for="allow_sundays">Permitir seleccionar domingos</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Años disponibles (separados por coma)</label>
                        <input type="text" name="available_years_input" id="available_years_input" class="form-control"
                            value="{{ implode(', ', old('available_years', $settings->available_years)) }}">
                        <div id="years-hidden-inputs">
                            @foreach (old('available_years', $settings->available_years) as $year)
                                <input type="hidden" name="available_years[]" value="{{ $year }}">
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Guardar configuración</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-soft p-4 mb-4">
            <h2 class="h5 fw-semibold mb-3">Días festivos</h2>
            <form id="holidayForm" class="row g-2 mb-3">
                <div class="col-md-5">
                    <input type="date" name="holiday_date" class="form-control" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="name" class="form-control" placeholder="Nombre del festivo" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">+</button>
                </div>
            </form>
            <ul class="list-group list-group-flush" id="holidays-list">
                @forelse ($holidays as $holiday)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0" data-id="{{ $holiday->id }}">
                        <span>{{ $holiday->holiday_date->format('d/m/Y') }} — {{ $holiday->name }}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-holiday-btn" data-id="{{ $holiday->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </li>
                @empty
                    <li class="list-group-item text-muted px-0" id="no-holidays">Sin días festivos registrados.</li>
                @endforelse
            </ul>
        </div>

        <div class="card-soft p-4">
            <h2 class="h5 fw-semibold mb-3">Días bloqueados</h2>
            <form id="blockedDateForm" class="row g-2 mb-3">
                <div class="col-md-5">
                    <input type="date" name="blocked_date" class="form-control" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="reason" class="form-control" placeholder="Motivo (opcional)">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">+</button>
                </div>
            </form>
            <ul class="list-group list-group-flush" id="blocked-list">
                @forelse ($blockedDates as $blockedDate)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0" data-id="{{ $blockedDate->id }}">
                        <span>{{ $blockedDate->blocked_date->format('d/m/Y') }} — {{ $blockedDate->reason ?? 'Bloqueado' }}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-blocked-btn" data-id="{{ $blockedDate->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </li>
                @empty
                    <li class="list-group-item text-muted px-0" id="no-blocked">Sin fechas bloqueadas.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.SettingsAdminConfig = {
    csrfToken: @json(csrf_token()),
    holidayStoreUrl: @json(route('admin.settings.holidays.store', absolute: false)),
    holidayDeleteUrl: '/admin/settings/holidays',
    blockedStoreUrl: @json(route('admin.settings.blocked-dates.store', absolute: false)),
    blockedDeleteUrl: '/admin/settings/blocked-dates',
};

document.getElementById('available_years_input')?.addEventListener('change', function () {
    const container = document.getElementById('years-hidden-inputs');
    container.innerHTML = '';
    this.value.split(',').map(y => y.trim()).filter(Boolean).forEach(year => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'available_years[]';
        input.value = year;
        container.appendChild(input);
    });
});
</script>
<script src="/js/admin-settings.js?v=8"></script>
@endpush
