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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Bootstrap CSS -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- html2canvas for image generation -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <!-- QRCode.js for QR code generation - Multiple CDN fallbacks -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.4.4/qrcode.js" integrity="sha512-oxrVyBhqnzQ0BzuM0A/6dEIk0alz0p4SpDRaWvtuUoarIc8rnL5lVniHG5Dp21MRFojcQcmKHjaskNXhSaUPPw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    {{-- link favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')


</head>
<body>
    <!-- Mobile backdrop -->
    <div id="sidebar-backdrop" class="sidebar-backdrop d-md-none" onclick="closeSidebar()"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Component -->
            @include('components.sidebar')

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

    <!-- SweetAlert2 Session Handling -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                });
            @endif

            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: "{{ session('warning') }}",
                });
            @endif

            // Global Delete Confirmation
            $(document).on('submit', '.delete-form', function(e) {
                e.preventDefault();
                const form = this;
                const message = $(this).data('message') || 'Data yang dihapus tidak dapat dikembalikan!';
                
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
