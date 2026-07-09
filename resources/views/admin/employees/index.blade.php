@extends('layouts.admin')

@section('title', 'Empleados')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-semibold mb-1">Administración de empleados</h1>
        <p class="text-muted mb-0">Crea, edita y administra el personal</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
        <i class="bi bi-plus-lg me-1"></i> Nuevo empleado
    </button>
</div>

<form method="GET" class="calendar-filters mb-4">
    <div class="row g-3">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre..." value="{{ $search }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary w-100">Buscar</button>
        </div>
    </div>
</form>

<div class="card-soft overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Color</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Vacaciones</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td><span class="legend-color" style="background: {{ $employee->color }}"></span></td>
                        <td class="fw-medium">{{ $employee->name }}</td>
                        <td>
                            @if ($employee->isActive())
                                <span class="badge bg-success-subtle text-success">Activo</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Bloqueado</span>
                            @endif
                        </td>
                        <td>{{ $employee->vacations_count }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary edit-employee-btn"
                                    data-employee='@json($employee)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info history-employee-btn"
                                    data-id="{{ $employee->id }}">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                @if ($employee->isActive())
                                    <button type="button" class="btn btn-outline-warning block-employee-btn"
                                        data-id="{{ $employee->id }}">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-success reactivate-employee-btn"
                                        data-id="{{ $employee->id }}">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                @endif
                                <button type="button" class="btn btn-outline-danger delete-employee-btn"
                                    data-id="{{ $employee->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay empleados registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $employees->links() }}</div>

@include('admin.employees._modals')
@endsection

@push('scripts')
<script>
window.EmployeeAdminConfig = {
    csrfToken: @json(csrf_token()),
    storeUrl: @json(route('admin.employees.store')),
    updateUrl: @json(url('/admin/employees')),
    deleteUrl: @json(url('/admin/employees')),
    blockUrl: @json(url('/admin/employees')),
    reactivateUrl: @json(url('/admin/employees')),
    historyUrl: @json(url('/admin/employees')),
};
</script>
<script src="/js/admin-employees.js?v=7"></script>
@endpush
