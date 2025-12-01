<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SMP N 3 SAWAN') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Bootstrap CSS -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- html2canvas for image generation -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <!-- QRCode.js for QR code generation - Multiple CDN fallbacks -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.4.4/qrcode.js" integrity="sha512-oxrVyBhqnzQ0BzuM0A/6dEIk0alz0p4SpDRaWvtuUoarIc8rnL5lVniHG5Dp21MRFojcQcmKHjaskNXhSaUPPw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            overflow-y: auto;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                width: 280px;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 25%;
            }
            .sidebar {
                width: 25%;
            }
        }

        @media (min-width: 992px) {
            .main-content {
                margin-left: 16.66667%;
            }
            .sidebar {
                width: 16.66667%;
            }
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
        }
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .content-area {
            padding: 2rem;
        }
        .navbar-brand {
            font-weight: 600;
        }

        /* Mobile overlay */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 999;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease-in-out;
        }

        .sidebar-backdrop.show {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body>
    <!-- Mobile backdrop -->
    <div id="sidebar-backdrop" class="sidebar-backdrop d-md-none" onclick="closeSidebar()"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="sidebar-brand text-center">
                    <img src="{{ asset('assets/image/LogoSMP3SAWAN.webp') }}" alt="Logo SMP 3 Sawan" class="me-2">
                    <span class="text-white fw-bold">SMPN 3 SAWAN</span>
                </div>

                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard Admin
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.guru') ? 'active' : '' }}" href="{{ route('admin.guru.index') }}">
                                    <i class="fas fa-chalkboard-teacher me-2"></i> Data Guru
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.siswa') ? 'active' : '' }}" href="{{ route('admin.siswa.index') }}">
                                    <i class="fas fa-user-graduate me-2"></i> Data Siswa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.tahun-pelajaran') || Str::startsWith(Route::currentRouteName(), 'admin.semester') ? 'active' : '' }}" href="{{ route('admin.tahun-pelajaran.index') }}">
                                    <i class="fas fa-calendar-alt me-2"></i> Tahun Pelajaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'school.profile' ? 'active' : '' }}" href="{{ route('school.profile') }}">
                                    <i class="fas fa-school me-2"></i> Data Sekolah
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-chart-bar me-2"></i> Laporan System
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-cogs me-2"></i> Pengaturan
                                </a>
                            </li>
                        @elseif(auth()->user()->isKepalaSekolah())
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'kepala-sekolah.dashboard' ? 'active' : '' }}" href="{{ route('kepala-sekolah.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-users me-2"></i> Data Guru & Siswa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'school.profile' ? 'active' : '' }}" href="{{ route('school.profile') }}">
                                    <i class="fas fa-school me-2"></i> Data Sekolah
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-chart-line me-2"></i> Laporan Akademik
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-calendar-alt me-2"></i> Jadwal Sekolah
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-dollar-sign me-2"></i> Keuangan
                                </a>
                            </li>
                        @elseif(auth()->user()->isGuru())
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'guru.dashboard' ? 'active' : '' }}" href="{{ route('guru.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Beranda
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-tasks me-2"></i> Manajemen Tugas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-upload me-2"></i> Kelola Materi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-check-square me-2"></i> Koreksi & Nilai
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'guru.absensi.siswa' ? 'active' : '' }}" href="{{ route('guru.absensi.siswa') }}">
                                    <i class="fas fa-user-check me-2"></i> Absensi Siswa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-calendar-alt me-2"></i> Kalender Jadwal
                                </a>
                            </li>
                        @elseif(auth()->user()->isSiswa())
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'siswa.dashboard' ? 'active' : '' }}" href="{{ route('siswa.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Beranda
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-book me-2"></i> Materi Pelajaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-tasks me-2"></i> Tugas Saya
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-chart-bar me-2"></i> Nilai
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-calendar-alt me-2"></i> Jadwal Kelas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-comments me-2"></i> Diskusi
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == 'profile.show' ? 'active' : '' }}" href="{{ route('profile.show')}}">
                                <i class="fas fa-user-cog me-2"></i> Profil Saya
                            </a>
                        </li>
                    </ul>

                    <hr class="text-white-50 mt-4">
                    <div class="px-3">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm w-100">
                                <i class="fas fa-sign-out-alt me-2"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top Navigation -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <button class="navbar-toggler d-md-none btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="h2 navbar-brand">@yield('title', 'Dashboard')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="text-muted">Selamat datang, {{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="content-area">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pass Laravel route to JavaScript
        window.currentRoute = '{{ Route::currentRouteName() }}';

        // Bootstrap collapse functionality for mobile sidebar
        document.addEventListener('DOMContentLoaded', function() {
            // Get elements
            const toggleButton = document.querySelector('[data-bs-toggle="collapse"]');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const mainContent = document.querySelector('.main-content');

            if (toggleButton && sidebar) {
                // Handle toggle button click
                toggleButton.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Check if we're on mobile
                    if (window.innerWidth < 768) {
                        const isExpanded = toggleButton.getAttribute('aria-expanded') === 'true';

                        if (isExpanded) {
                            closeSidebar();
                        } else {
                            openSidebar();
                        }
                    }
                });
            }

            // Close sidebar when clicking outside on mobile
            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }

            // Close sidebar when clicking on main content area on mobile
            if (mainContent) {
                mainContent.addEventListener('click', function(e) {
                    if (window.innerWidth < 768 && sidebar.classList.contains('show')) {
                        // Don't close if clicking on the toggle button
                        if (!toggleButton.contains(e.target)) {
                            closeSidebar();
                        }
                    }
                });
            }

            // Close sidebar when pressing Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && window.innerWidth < 768 && sidebar.classList.contains('show')) {
                    closeSidebar();
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeSidebar();
                }
            });

            // Prevent scrolling on body when sidebar is open on mobile
            function toggleBodyScroll(disable) {
                if (window.innerWidth < 768) {
                    if (disable) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                }
            }

            // Update the open and close functions to handle body scroll
            window.openSidebar = function() {
                const sidebar = document.getElementById('sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');
                const toggleButton = document.querySelector('[data-bs-toggle="collapse"]');

                if (sidebar) {
                    sidebar.classList.add('show');
                    toggleBodyScroll(true);
                }
                if (backdrop) {
                    backdrop.classList.add('show');
                }
                if (toggleButton) {
                    toggleButton.setAttribute('aria-expanded', 'true');
                }
            };

            window.closeSidebar = function() {
                const sidebar = document.getElementById('sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');
                const toggleButton = document.querySelector('[data-bs-toggle="collapse"]');

                if (sidebar) {
                    sidebar.classList.remove('show');
                    toggleBodyScroll(false);
                }
                if (backdrop) {
                    backdrop.classList.remove('show');
                }
                if (toggleButton) {
                    toggleButton.setAttribute('aria-expanded', 'false');
                }
            };
        });

        function openSidebar() {
            window.openSidebar();
        }

        function closeSidebar() {
            window.closeSidebar();
        }
    </script>
    @stack('scripts')
</body>
</html>
