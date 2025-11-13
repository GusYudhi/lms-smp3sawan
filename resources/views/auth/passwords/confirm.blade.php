@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <img src="{{ asset('assets/image/LogoSMP3SAWAN.webp') }}" alt="Logo SMP 3 SAWAN" style="width: 80px; height: 80px; object-fit: contain; margin-bottom: 1rem;">
        <h3 class="mb-0">Konfirmasi Kata Sandi</h3>
        <p class="mb-0 small">Portal SMPN 3 SAWAN</p>
    </div>

    <div class="auth-body">
        <p class="text-muted mb-4 text-center">Silakan konfirmasi kata sandi Anda sebelum melanjutkan</p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-3">
                <input id="password"
                       type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password"
                       placeholder="Kata Sandi"
                       required
                       autocomplete="current-password">

                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check me-2"></i>Konfirmasi Kata Sandi
            </button>
        </form>
    </div>

    <div class="auth-footer">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forgot-password">
                <i class="fas fa-key me-1"></i>Lupa Kata Sandi?
            </a>
        @endif
    </div>
</div>
@endsection
