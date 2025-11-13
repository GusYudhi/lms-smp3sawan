<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
</head>
<body>
    <div class="lms-container">
        <aside class="sidebar">
            <div class="logo-section">
                <img src="{{ asset('assets/image/LogoSMP3SAWAN.webp') }}" alt="Logo SMP 3 Sawan" class="logo-image">
                <span class="logo-text">SMPN 3 SAWAN</span>
            </div>
            <nav class="main-nav">
                <ul>
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item" data-page="admin-dashboard"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Admin</a></li>
                        <li class="nav-item" data-page="data-guru"><a href="{{ route('admin.guru.index') }}"><i class="fas fa-chalkboard-teacher"></i> Data Guru</a></li>
                        <li class="nav-item" data-page="data-siswa"><a href="{{ route('admin.siswa.index') }}"><i class="fas fa-user-graduate"></i> Data Siswa</a></li>
                        <li class="nav-item" data-page="data-sekolah"><a href="{{ route('school.profile') }}"><i class="fas fa-school"></i> Data Sekolah</a></li>
                        <li class="nav-item" data-page="laporan-system"><a href="#"><i class="fas fa-chart-bar"></i> Laporan System</a></li>
                        <li class="nav-item" data-page="pengaturan"><a href="#"><i class="fas fa-cogs"></i> Pengaturan</a></li>
                    @elseif(auth()->user()->isKepalaSekolah())
                        <li class="nav-item" data-page="dashboard"><a href="{{ route('kepala-sekolah.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li class="nav-item" data-page="data-guru-siswa"><a href="#"><i class="fas fa-users"></i> Data Guru & Siswa</a></li>
                        <li class="nav-item" data-page="data-sekolah"><a href="{{ route('school.profile') }}"><i class="fas fa-school"></i> Data Sekolah</a></li>
                        <li class="nav-item" data-page="laporan-akademik"><a href="#"><i class="fas fa-chart-line"></i> Laporan Akademik</a></li>
                        <li class="nav-item" data-page="jadwal-sekolah"><a href="#"><i class="fas fa-calendar-alt"></i> Jadwal Sekolah</a></li>
                        <li class="nav-item" data-page="keuangan"><a href="#"><i class="fas fa-dollar-sign"></i> Keuangan</a></li>
                    @elseif(auth()->user()->isGuru())
                        <li class="nav-item" data-page="beranda"><a href="{{ route('guru.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Beranda</a></li>
                        <li class="nav-item" data-page="manajemen-tugas"><a href="#"><i class="fas fa-tasks"></i> Manajemen Tugas</a></li>
                        <li class="nav-item" data-page="kelola-materi"><a href="#"><i class="fas fa-upload"></i> Kelola Materi</a></li>
                        <li class="nav-item" data-page="koreksi-nilai"><a href="#"><i class="fas fa-check-square"></i> Koreksi & Nilai</a></li>
                        <li class="nav-item" data-page="absensi-kelas"><a href="#"><i class="fas fa-user-check"></i> Absensi Kelas</a></li>
                        <li class="nav-item" data-page="kalender-jadwal"><a href="#"><i class="fas fa-calendar-alt"></i> Kalender Jadwal</a></li>
                    @elseif(auth()->user()->isSiswa())
                        <li class="nav-item" data-page="beranda"><a href="{{ route('siswa.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Beranda</a></li>
                        <li class="nav-item" data-page="materi-pelajaran"><a href="#"><i class="fas fa-book"></i> Materi Pelajaran</a></li>
                        <li class="nav-item" data-page="tugas-saya"><a href="#"><i class="fas fa-tasks"></i> Tugas Saya</a></li>
                        <li class="nav-item" data-page="nilai"><a href="#"><i class="fas fa-chart-bar"></i> Nilai</a></li>
                        <li class="nav-item" data-page="jadwal-kelas"><a href="#"><i class="fas fa-calendar-alt"></i> Jadwal Kelas</a></li>
                        <li class="nav-item" data-page="diskusi"><a href="#"><i class="fas fa-comments"></i> Diskusi</a></li>
                    @endif
                    <li class="nav-item" data-page="profil-saya"><a href="{{ route('profile.show')}}"><i class="fas fa-user-cog"></i> Profil Saya</a></li>
                </ul>
            </nav>
            <div class="logout-section">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-content">

            <header class="main-header">
                <button id="sidebar-toggle" class="btn-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                {{-- <h1 class="page-title">@yield('title', 'Dashboard')</h1> --}}
            </header>

            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
        // Pass Laravel route to JavaScript
        window.currentRoute = '{{ Route::currentRouteName() }}';
    </script>
    <script src="{{ asset('assets/app.js') }}"></script>
</body>
</html>
