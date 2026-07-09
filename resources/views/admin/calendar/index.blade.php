@extends('layouts.admin')

@section('title', 'Calendario general')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 page-title">
    <div>
        <h1 class="h3 fw-semibold mb-1 brand-title">Calendario general</h1>
        <p class="text-muted mb-0">Visualiza y administra las vacaciones del personal</p>
    </div>
    <div class="d-flex flex-wrap gap-2 no-print">
        <a href="#" id="export-print" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> Imprimir</a>
        <a href="#" id="export-pdf" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-pdf me-1"></i> PDF</a>
        <a href="#" id="export-excel" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
    </div>
</div>

<div class="calendar-filters no-print">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Empleado</label>
            <select id="filter-employee" class="form-select">
                <option value="">Todos</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Mes</label>
            <select id="filter-month" class="form-select">
                <option value="">Todos</option>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Año</label>
            <select id="filter-year" class="form-select">
                <option value="">Todos</option>
                @foreach ($calendarConfig['availableYears'] as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Búsqueda</label>
            <input type="text" id="filter-search" class="form-control" placeholder="Buscar por nombre...">
        </div>
        <div class="col-md-2 d-grid gap-2">
            <button type="button" id="apply-filters" class="btn btn-primary">Aplicar</button>
            <button type="button" id="clear-filters" class="btn btn-outline-secondary btn-sm">Limpiar</button>
        </div>
    </div>
    <div id="filter-feedback" class="filter-feedback">Mostrando todas las vacaciones</div>
</div>

<div id="admin-calendar"></div>

<div class="card-soft p-3 mt-4">
    <h2 class="h6 fw-semibold mb-2">Leyenda de colores</h2>
    <div id="color-legend" class="d-flex flex-wrap"></div>
</div>

<div class="modal fade" id="vacationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold">Detalle de vacación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Empleado:</strong> <span id="modal-employee-name"></span></p>
                <p class="mb-0"><strong>Fecha:</strong> <span id="modal-vacation-date"></span></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="delete-vacation-btn" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i> Eliminar vacación
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.AdminCalendarConfig = {
    calendarConfig: @json($calendarConfig),
    eventsUrl: @json(route('api.calendar.events')),
    blockedUrl: @json(route('api.calendar.blocked-dates')),
    legendUrl: @json(route('api.calendar.legend')),
    deleteVacationUrl: @json(url('/admin/vacations')),
    exportPrintUrl: @json(route('admin.exports.print')),
    exportPdfUrl: @json(route('admin.exports.pdf')),
    exportExcelUrl: @json(route('admin.exports.excel')),
    csrfToken: @json(csrf_token()),
};
</script>
<script src="{{ asset('js/admin-calendar.js') }}?v=6"></script>
@endpush
