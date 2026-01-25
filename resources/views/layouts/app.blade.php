<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Visi: Terwujud Lulusan yang Unggul dalam Prestasi, Berkarakter, dan Berwawasan lingkungan">


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

    {{-- link favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}">

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
                    <img src="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}" alt="Logo SMP 3 Sawan" class="me-2">
                    <span class="text-white fw-bold">SMP NEGERI 3 SAWAN</span>
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
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.absensi') ? 'active' : '' }}" href="{{ route('admin.absensi.index') }}">
                                    <i class="fas fa-clipboard-check me-2"></i> Data Absensi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.kegiatan-kokurikuler') ? 'active' : '' }}" href="{{ route('admin.kegiatan-kokurikuler.index') }}">
                                    <i class="fas fa-running me-2"></i> Kokurikuler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.prestasi') ? 'active' : '' }}" href="{{ route('admin.prestasi.index') }}">
                                    <i class="fas fa-trophy me-2"></i> Prestasi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.berita') ? 'active' : '' }}" href="{{ route('admin.berita.index') }}">
                                    <i class="fas fa-newspaper me-2"></i> Berita
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.saran') ? 'active' : '' }}" href="{{ route('admin.saran.index') }}">
                                    <i class="fas fa-envelope me-2"></i> Kotak Saran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'admin.galeri') ? 'active' : '' }}" href="{{ route('admin.galeri.index') }}">
                                    <i class="fas fa-images me-2"></i> Galeri Sekolah
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'school.profile' ? 'active' : '' }}" href="{{ route('school.profile') }}">
                                    <i class="fas fa-school me-2"></i> Data Sekolah
                                </a>
                            </li>
                        @elseif(auth()->user()->isKepalaSekolah())
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'kepala-sekolah.dashboard' ? 'active' : '' }}" href="{{ route('kepala-sekolah.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a>
                            </li>

                            <!-- Data Guru -->
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'kepala-sekolah.guru') ? 'active' : '' }}" href="{{ route('kepala-sekolah.guru.index') }}">
                                    <i class="fas fa-chalkboard-teacher me-2"></i> Data Guru
                                </a>
                            </li>

                            <!-- Data Siswa -->
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'kepala-sekolah.siswa') ? 'active' : '' }}" href="{{ route('kepala-sekolah.siswa.index') }}">
                                    <i class="fas fa-user-graduate me-2"></i> Data Siswa
                                </a>
                            </li>

                            <!-- Tahun Pelajaran & Semester -->
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'kepala-sekolah.tahun-pelajaran') || str_contains(Route::currentRouteName(), 'kepala-sekolah.semester') ? 'active' : '' }}" href="{{ route('kepala-sekolah.tahun-pelajaran.index') }}">
                                    <i class="fas fa-calendar-alt me-2"></i> Tahun Pelajaran
                                </a>
                            </li>

                            <!-- Data Absensi -->
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'kepala-sekolah.absensi') ? 'active' : '' }}" href="{{ route('kepala-sekolah.absensi.index') }}">
                                    <i class="fas fa-clipboard-check me-2"></i> Data Absensi
                                </a>
                            </li>

                            <!-- Jadwal Pelajaran -->
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'kepala-sekolah.jadwal-pelajaran') ? 'active' : '' }}" href="{{ route('kepala-sekolah.jadwal-pelajaran.index') }}">
                                    <i class="fas fa-calendar-alt me-2"></i> Jadwal Pelajaran
                                </a>
                            </li>

                            <!-- Tugas Guru -->
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'kepala-sekolah.tugas-guru') ? 'active' : '' }}" href="{{ route('kepala-sekolah.tugas-guru.index') }}">
                                    <i class="fas fa-tasks me-2"></i> Tugas Guru
                                </a>
                            </li>

                            <!-- Jurnal Mengajar -->
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'kepala-sekolah.jurnal-mengajar') ? 'active' : '' }}" href="{{ route('kepala-sekolah.jurnal-mengajar.index') }}">
                                    <i class="fas fa-book me-2"></i> Jurnal Mengajar
                                </a>
                            </li>

                            <!-- Agenda Guru -->
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'kepala-sekolah.agenda-guru') ? 'active' : '' }}" href="{{ route('kepala-sekolah.agenda-guru.index') }}">
                                    <i class="fas fa-clipboard-list me-2"></i> Agenda Guru
                                </a>
                            </li>

                            <!-- Agenda Kepala Sekolah -->
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'kepala-sekolah.agenda') && !str_contains(Route::currentRouteName(), 'kepala-sekolah.agenda-guru') ? 'active' : '' }}" href="{{ route('kepala-sekolah.agenda') }}">
                                    <i class="fas fa-calendar-alt me-2"></i> Agenda Saya
                                </a>
                            </li>

                            <!-- Manajemen Konten -->
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'kepala-sekolah.kegiatan-kokurikuler') ? 'active' : '' }}" href="{{ route('kepala-sekolah.kegiatan-kokurikuler.index') }}">
                                    <i class="fas fa-running me-2"></i> Kokurikuler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'kepala-sekolah.prestasi') ? 'active' : '' }}" href="{{ route('kepala-sekolah.prestasi.index') }}">
                                    <i class="fas fa-trophy me-2"></i> Prestasi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'kepala-sekolah.berita') ? 'active' : '' }}" href="{{ route('kepala-sekolah.berita.index') }}">
                                    <i class="fas fa-newspaper me-2"></i> Berita
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'kepala-sekolah.galeri') ? 'active' : '' }}" href="{{ route('kepala-sekolah.galeri.index') }}">
                                    <i class="fas fa-images me-2"></i> Galeri Sekolah
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'kepala-sekolah.saran') ? 'active' : '' }}" href="{{ route('kepala-sekolah.saran.index') }}">
                                    <i class="fas fa-envelope me-2"></i> Kotak Saran
                                </a>
                            </li>

                            <!-- Data Sekolah -->
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'school.profile' ? 'active' : '' }}" href="{{ route('school.profile') }}">
                                    <i class="fas fa-school me-2"></i> Data Sekolah
                                </a>
                            </li>
                        @elseif(auth()->user()->isGuru())
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'guru.dashboard' ? 'active' : '' }}" href="{{ route('guru.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Beranda
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'guru.absensi.siswa' ? 'active' : '' }}" href="{{ route('guru.absensi.siswa') }}">
                                    <i class="fas fa-user-check me-2"></i> Absensi Siswa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'guru.rekap-absensi.siswa') ? 'active' : '' }}" href="{{ route('guru.rekap-absensi.siswa') }}">
                                    <i class="fas fa-clipboard-check me-2"></i> Rekap Absensi Siswa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'guru.absensi-guru' ? 'active' : '' }}" href="{{ route('guru.absensi-guru') }}">
                                    <i class="fas fa-camera me-2"></i> Absensi Guru
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'guru.jadwal-mengajar.today' ? 'active' : '' }}" href="{{ route('guru.jadwal-mengajar.today') }}">
                                    <i class="fas fa-calendar-day me-2"></i> Jadwal Hari Ini
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'guru.jadwal-mengajar' ? 'active' : '' }}" href="{{ route('guru.jadwal-mengajar') }}">
                                    <i class="fas fa-chalkboard-teacher me-2"></i> Jadwal Mengajar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'guru.jurnal-mengajar') ? 'active' : '' }}" href="{{ route('guru.jurnal-mengajar.index') }}">
                                    <i class="fas fa-book me-2"></i> Jurnal Mengajar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'guru.agenda') ? 'active' : '' }}" href="{{ route('guru.agenda') }}">
                                    <i class="fas fa-clipboard-list me-2"></i> Agenda Guru
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'guru.tugas-guru') ? 'active' : '' }}" href="{{ route('guru.tugas-guru.index') }}">
                                    <i class="fas fa-tasks me-2"></i> Tugas Guru
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'guru.materi') ? 'active' : '' }}" href="{{ route('guru.materi.index') }}">
                                    <i class="fas fa-book-open me-2"></i> Materi Pelajaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'kegiatan-kokurikuler') ? 'active' : '' }}" href="{{ route('kegiatan-kokurikuler.index') }}">
                                    <i class="fas fa-running me-2"></i> Info Kokurikuler
                                </a>
                            </li>
                        @elseif(auth()->user()->isSiswa())
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() == 'siswa.dashboard' ? 'active' : '' }}" href="{{ route('siswa.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Beranda
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'siswa.absensi') ? 'active' : '' }}" href="{{ route('siswa.absensi.index') }}">
                                    <i class="fas fa-user-check me-2"></i> Absensi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ str_contains(Route::currentRouteName(), 'siswa.jadwal-pelajaran') ? 'active' : '' }}" href="{{ route('siswa.jadwal-pelajaran.index') }}">
                                    <i class="fas fa-calendar-alt me-2"></i> Jadwal Pelajaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'siswa.materi') ? 'active' : '' }}" href="{{ route('siswa.materi.index') }}">
                                    <i class="fas fa-book-reader me-2"></i> Materi Belajar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'siswa.absensi-mapel') ? 'active' : '' }}" href="{{ route('siswa.absensi-mapel.index') }}">
                                    <i class="fas fa-clipboard-list me-2"></i> Absensi Mapel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'kegiatan-kokurikuler') ? 'active' : '' }}" href="{{ route('kegiatan-kokurikuler.index') }}">
                                    <i class="fas fa-running me-2"></i> Info Kokurikuler
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
                    <div class="px-3" style="margin-bottom: 80px;">
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
