@extends('layouts.guest')

@section('title', 'Ingresa tu nombre')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card-soft p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="stat-icon bg-primary-subtle text-primary mx-auto mb-3">
                    <i class="bi bi-person-badge"></i>
                </div>
                <h2 class="h4 fw-semibold mb-2 brand-title">Bienvenido</h2>
                <p class="text-muted mb-0">Escribe tu nombre completo para registrar o continuar con tus vacaciones</p>
            </div>

            <form action="{{ route('guest.name.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="form-label fw-medium">Nombre completo</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control form-control-lg @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="Ej. María González"
                        autofocus
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    Continuar <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
