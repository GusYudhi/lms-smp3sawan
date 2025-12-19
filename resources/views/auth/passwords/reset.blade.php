@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <img src="{{ asset('assets/image/LogoSMP3SAWAN.webp') }}" alt="Logo SMP 3 SAWAN" style="width: 80px; height: 80px; object-fit: contain; margin-bottom: 1rem;">
        <h3 class="mb-0">Reset Kata Sandi</h3>
        <p class="mb-0 small">Portal SMPN 3 SAWAN</p>
    </div>

    <div class="auth-body">
        <p class="text-muted mb-4 text-center">Buat kata sandi baru untuk akun Anda</p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <input id="email"
                       type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ $email ?? old('email') }}"
                       placeholder="Alamat Email"
                       required
                       autocomplete="email"
                       autofocus>

                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input id="password"
                       type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password"
                       placeholder="Kata Sandi Baru"
                       required
                       autocomplete="new-password">

                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <input id="password-confirm"
                       type="password"
                       class="form-control"
                       name="password_confirmation"
                       placeholder="Konfirmasi Kata Sandi"
                       required
                       autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key me-2"></i>Reset Kata Sandi
            </button>
        </form>
    </div>

    <div class="auth-footer">
        <a href="{{ route('login') }}" class="forgot-password">
            <i class="fas fa-arrow-left me-1"></i>Masuk ke Akun Anda
        </a>
    </div>
</div>
@endsection
