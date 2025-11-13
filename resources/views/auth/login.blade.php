@extends('layouts.auth')

@section('content')
<div class="login-page">
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-school logo-icon"></i>
            <h2>Selamat Datang di Portal Guru <br> SMPN 3 SAWAN</h2>
        </div>
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <input id="email"
                   type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="Email"
                   required
                   autocomplete="email"
                   autofocus>

            @error('email')
                <span class="invalid-feedback" role="alert" style="display: block; color: #f44336; font-size: 12px; margin-top: -10px; margin-bottom: 10px;">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <input id="password"
                   type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   name="password"
                   placeholder="Kata Sandi"
                   required
                   autocomplete="current-password">

            @error('password')
                <span class="invalid-feedback" role="alert" style="display: block; color: #f44336; font-size: 12px; margin-top: -10px; margin-bottom: 10px;">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <button type="submit" class="btn-login">Masuk</button>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-password">Lupa Kata Sandi?</a>
            @endif
        </form>
    </div>
</div>
@endsection