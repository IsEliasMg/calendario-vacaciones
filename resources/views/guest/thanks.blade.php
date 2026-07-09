@extends('layouts.guest')

@section('title', 'Registro exitoso')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card-soft p-5 text-center">
            <div class="stat-icon bg-success-subtle text-success mx-auto mb-3" style="width:64px;height:64px;font-size:1.75rem;">
                <i class="bi bi-check-lg"></i>
            </div>
            <h2 class="h4 fw-semibold text-success mb-3">¡Listo!</h2>
            <p class="lead mb-4">Las vacaciones fueron registradas correctamente.</p>
            <a href="{{ route('guest.name') }}" class="btn btn-primary">
                <i class="bi bi-house me-1"></i> Volver al inicio
            </a>
        </div>
    </div>
</div>
@endsection
