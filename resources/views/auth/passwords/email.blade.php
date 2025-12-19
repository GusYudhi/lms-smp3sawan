@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <img src="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}" alt="Logo SMP 3 SAWAN" style="width: 80px; height: 80px; object-fit: contain; margin-bottom: 1rem;">
        <h3 class="mb-0">Reset Kata Sandi</h3>
        <p class="mb-0 small">Portal SMPN 3 SAWAN</p>
    </div>

    <div class="auth-body">
        @if (session('status'))
            <div class="alert alert-success" role="alert" style="border-radius: 12px; border: none; background-color: #d4edda; color: #155724; margin-bottom: 1.5rem;">
                <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
            </div>
        @endif

        <p class="text-muted mb-4 text-center">Masukkan alamat email Anda untuk menerima link reset kata sandi</p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <input id="email"
                       type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ old('email') }}"
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

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Kirim Link Reset
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
