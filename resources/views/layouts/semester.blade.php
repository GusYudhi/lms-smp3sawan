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

        .semester-info-card {
            background: rgba(255,255,255,0.1);
            border-radius: 0.5rem;
            padding: 1rem;
            margin: 1rem;
            border-left: 4px solid #ffc107;
        }
    </style>

    @stack('styles')
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

                <!-- Semester Info -->
                <div class="semester-info-card">
                    <div class="text-white">
                        <small class="d-block mb-1 opacity-75">Semester Aktif</small>
                        <div class="fw-bold">{{ $semester->tahunPelajaran->nama }}</div>
                        <div class="small">{{ $semester->nama }}</div>
                    </div>
                </div>

                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <!-- Back Button -->
                        <li class="nav-item mb-3">
                            <a class="nav-link bg-white bg-opacity-10" href="{{ route('admin.tahun-pelajaran.dashboard', $semester->tahunPelajaran->id) }}">
                                <i class="fas fa-arrow-left me-2"></i> Kembali ke Tahun Pelajaran
                            </a>
                        </li>

                        <!-- Dashboard Semester -->
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() == 'admin.semester.dashboard' ? 'active' : '' }}" href="{{ route('admin.semester.dashboard', $semester->id) }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard Semester
                            </a>
                        </li>

                        <li class="nav-item">
                            <div class="dropdown-divider bg-white opacity-25 my-2"></div>
                            <small class="text-white opacity-50 px-3 d-block mb-2">MANAJEMEN AKADEMIK</small>
                        </li>

                        <!-- Mata Pelajaran -->
                        <li class="nav-item">
                            <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.mapel') ? 'active' : '' }}" href="{{ route('admin.mapel.index', ['semester_id' => $semester->id]) }}">
                                <i class="fas fa-book me-2"></i> Mata Pelajaran
                            </a>
                        </li>

                        <!-- Jadwal Pelajaran -->
                        <li class="nav-item">
                            <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.jadwal') ? 'active' : '' }}" href="{{ route('admin.jadwal.index', ['semester_id' => $semester->id]) }}">
                                <i class="fas fa-calendar-day me-2"></i> Jadwal Pelajaran
                            </a>
                        </li>

                        <!-- Jam Pelajaran -->
                        <li class="nav-item">
                            <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.jam-pelajaran') ? 'active' : '' }}" href="{{ route('admin.jam-pelajaran.index', ['semester_id' => $semester->id]) }}">
                                <i class="fas fa-clock me-2"></i> Jam Pelajaran
                            </a>
                        </li>

                        <!-- Jadwal Tetap -->
                        <li class="nav-item">
                            <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.fixed-schedule') ? 'active' : '' }}" href="{{ route('admin.fixed-schedule.index', ['semester_id' => $semester->id]) }}">
                                <i class="fas fa-calendar-check me-2"></i> Jadwal Tetap
                            </a>
                        </li>
                    </ul>

                    <hr class="text-white-50 mt-4">
                    <div class="px-3">
                        <div class="text-white small mb-2">
                            <i class="fas fa-user me-2"></i>{{ auth()->user()->name }}
                        </div>
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
                    <button class="navbar-toggler d-md-none btn btn-outline-primary" type="button" onclick="openSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="h2 navbar-brand">@yield('title', 'Dashboard Semester')</h1>
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
        // Bootstrap collapse functionality for mobile sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const mainContent = document.querySelector('.main-content');

            // Close sidebar when clicking outside on mobile
            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
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

            // Open sidebar function
            window.openSidebar = function() {
                if (sidebar) {
                    sidebar.classList.add('show');
                    toggleBodyScroll(true);
                }
                if (backdrop) {
                    backdrop.classList.add('show');
                }
            };

            // Close sidebar function
            window.closeSidebar = function() {
                if (sidebar) {
                    sidebar.classList.remove('show');
                    toggleBodyScroll(false);
                }
                if (backdrop) {
                    backdrop.classList.remove('show');
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
