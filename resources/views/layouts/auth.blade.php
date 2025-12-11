<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SMP N 3 SAWAN') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/image/LogoSMP3SAWAN.webp') }}">


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Bootstrap CSS -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            background: #0d47a1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="4"/></g></svg>');
            z-index: 1;
        }
        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
            margin: 2rem;
            position: relative;
            z-index: 2;
        }
        .auth-header {
            background: #0d47a1;
            color: white;
            text-align: center;
            padding: 3rem 2rem 2rem;
            position: relative;
        }
        .auth-header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            background: white;
            border-radius: 20px 20px 0 0;
        }
        .auth-header i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        .auth-header h3 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .auth-body {
            padding: 2.5rem 2rem 2rem;
        }
        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 15px 18px;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #0d47a1;
            box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.15);
            transform: translateY(-1px);
        }
        .form-control::placeholder {
            color: #adb5bd;
            font-weight: 400;
        }
        .btn-primary {
            background: #0d47a1;
            border: none;
            border-radius: 12px;
            padding: 15px;
            width: 100%;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        .btn-primary:hover {
            background: #0a3d8a;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 71, 161, 0.4);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .form-check {
            margin-bottom: 1.5rem;
        }
        .form-check-input:checked {
            background-color: #0d47a1;
            border-color: #0d47a1;
        }
        .form-check-input:focus {
            border-color: #0d47a1;
            box-shadow: 0 0 0 0.25rem rgba(13, 71, 161, 0.25);
        }
        .auth-footer {
            text-align: center;
            padding: 1rem 2rem 2.5rem;
        }
        .forgot-password {
            color: #0d47a1;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .forgot-password:hover {
            color: #0a3d8a;
            text-decoration: none;
            transform: translateY(-1px);
        }
        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        .is-invalid {
            border-color: #dc3545;
        }
        .is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
