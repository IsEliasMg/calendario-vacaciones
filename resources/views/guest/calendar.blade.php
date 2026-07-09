@extends('layouts.guest')

@section('title', 'Selecciona tus vacaciones')

@section('content')
<div class="card-soft p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h4 fw-semibold mb-1 brand-title">Hola, {{ $employee->name }}</h2>
            <p class="text-muted mb-0">Selecciona o quita tus días. Solo verás tus vacaciones.</p>
        </div>
        <a href="{{ route('guest.name') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Cambiar nombre
        </a>
    </div>

    <div class="color-picker-panel mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h3 class="h6 fw-semibold mb-1">Tu color de vacaciones</h3>
                <p class="text-muted small mb-0">Por defecto es gris. Cámbialo al color que quieras para tus días.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span id="current-color-preview" class="legend-color" style="width:22px;height:22px;background: {{ $employee->color }}"></span>
                <input type="color" id="employee-color-input" value="{{ $employee->color }}" class="form-control form-control-color" title="Elegir color">
                <button type="button" id="save-color-btn" class="btn btn-sm btn-primary">Guardar color</button>
            </div>
        </div>
        <div id="color-feedback" class="small mt-2 text-muted"></div>
    </div>

    <div id="guest-calendar"></div>

    <div class="mt-4">
        <h3 class="h6 fw-semibold mb-2">Días seleccionados</h3>
        <div id="selected-dates-list" class="min-height-40">
            <span class="text-muted" id="no-dates-msg">Aún no has seleccionado días</span>
        </div>
    </div>

    <form id="vacation-form" action="{{ route('guest.vacations.store') }}" method="POST" class="mt-4">
        @csrf
        <div id="dates-inputs"></div>
        <button type="button" id="save-btn" class="btn btn-primary btn-lg">
            <i class="bi bi-check2-circle me-1"></i> Guardar vacaciones
        </button>
    </form>
</div>
@endsection

@push('modals')
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold" id="confirmModalLabel">Confirmar vacaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Confirmas guardar estos días de vacaciones?</p>
                <p class="text-muted small mb-2">Si quitas días, también se actualizarán.</p>
                <ul id="confirm-dates-list" class="mb-0"></ul>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="confirm-save-btn" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/es.global.min.js"></script>
<script>
window.GuestCalendarConfig = {
    calendarConfig: @json($calendarConfig),
    existingDates: @json($existingDates),
    eventsUrl: @json(route('api.calendar.events')),
    blockedUrl: @json(route('api.calendar.blocked-dates')),
    colorUrl: @json(route('guest.vacations.color')),
    employeeId: {{ $employee->id }},
    employeeColor: @json($employee->color),
    maxDays: {{ $calendarConfig['maxDaysPerEmployee'] ?? 15 }},
    csrfToken: @json(csrf_token()),
};
</script>
<script src="{{ asset('js/guest-calendar.js') }}?v=6"></script>
@endpush
