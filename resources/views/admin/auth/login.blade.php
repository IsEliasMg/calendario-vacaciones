@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card-soft p-4 p-md-5">
            <div class="text-center mb-4">
                <img src="/logoo.png" alt="Logo institucional" class="brand-logo brand-logo-login mb-3">
                <h1 class="h4 fw-semibold mt-2 brand-title">Panel administrativo</h1>
                <p class="text-muted">Ingresa tus credenciales</p>
            </div>

            <form action="{{ route('admin.login.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Recordarme</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
        </div>
    </div>
</div>
@endsection
