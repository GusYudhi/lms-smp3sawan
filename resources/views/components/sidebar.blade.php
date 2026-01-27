<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="sidebar-brand text-center">
        <img src="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}" alt="Logo SMP 3 Sawan" class="me-2">
        <span class="text-white fw-bold">SMP NEGERI 3 SAWAN</span>
    </div>

    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            @if(auth()->user()->isAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard Admin
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}" href="{{ route('admin.guru.index') }}">
                        <i class="fas fa-chalkboard-teacher me-2"></i> Data Guru
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}" href="{{ route('admin.siswa.index') }}">
                        <i class="fas fa-user-graduate me-2"></i> Data Siswa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}" href="{{ route('admin.kelas.index') }}">
                        <i class="fas fa-school me-2"></i> Data Kelas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tahun-pelajaran.*') || request()->routeIs('admin.semester.*') ? 'active' : '' }}" href="{{ route('admin.tahun-pelajaran.index') }}">
                        <i class="fas fa-calendar-alt me-2"></i> Tahun Pelajaran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}" href="{{ route('admin.absensi.index') }}">
                        <i class="fas fa-clipboard-check me-2"></i> Data Absensi QR Code Siswa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('rekap-jurnal.*') ? 'active' : '' }}" href="{{ route('rekap-jurnal.index') }}">
                        <i class="fas fa-book-open me-2"></i> Rekap Jurnal & Absensi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.kegiatan-kokurikuler.*') ? 'active' : '' }}" href="{{ route('admin.kegiatan-kokurikuler.index') }}">
                        <i class="fas fa-running me-2"></i> Kokurikuler
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.prestasi.*') ? 'active' : '' }}" href="{{ route('admin.prestasi.index') }}">
                        <i class="fas fa-trophy me-2"></i> Prestasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}" href="{{ route('admin.berita.index') }}">
                        <i class="fas fa-newspaper me-2"></i> Berita
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.saran.*') ? 'active' : '' }}" href="{{ route('admin.saran.index') }}">
                        <i class="fas fa-envelope me-2"></i> Kotak Saran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.galeri.*') ? 'active' : '' }}" href="{{ route('admin.galeri.index') }}">
                        <i class="fas fa-images me-2"></i> Galeri Sekolah
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('school.profile') ? 'active' : '' }}" href="{{ route('school.profile') }}">
                        <i class="fas fa-school me-2"></i> Data Sekolah
                    </a>
                </li>
            @elseif(auth()->user()->isKepalaSekolah())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.dashboard') ? 'active' : '' }}" href="{{ route('kepala-sekolah.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>

                <!-- Data Guru -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.guru.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.guru.index') }}">
                        <i class="fas fa-chalkboard-teacher me-2"></i> Data Guru
                    </a>
                </li>

                <!-- Data Siswa -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.siswa.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.siswa.index') }}">
                        <i class="fas fa-user-graduate me-2"></i> Data Siswa
                    </a>
                </li>

                <!-- Tahun Pelajaran & Semester -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.tahun-pelajaran.*') || request()->routeIs('kepala-sekolah.semester.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.tahun-pelajaran.index') }}">
                        <i class="fas fa-calendar-alt me-2"></i> Tahun Pelajaran
                    </a>
                </li>

                <!-- Data Absensi -->
                <li class="nav-item">
                    <a class="nav-link {{ str_contains(Route::currentRouteName(), 'kepala-sekolah.absensi') ? 'active' : '' }}" href="{{ route('kepala-sekolah.absensi.index') }}">
                        <i class="fas fa-clipboard-check me-2"></i> Data Absensi
                    </a>
                </li>

                <!-- Rekap Jurnal & Absensi -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('rekap-jurnal.*') ? 'active' : '' }}" href="{{ route('rekap-jurnal.index') }}">
                        <i class="fas fa-book-open me-2"></i> Rekap Jurnal & Absensi
                    </a>
                </li>

                <!-- Jadwal Pelajaran -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.jadwal-pelajaran.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.jadwal-pelajaran.index') }}">
                        <i class="fas fa-calendar-alt me-2"></i> Jadwal Pelajaran
                    </a>
                </li>

                <!-- Tugas Guru -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.tugas-guru.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.tugas-guru.index') }}">
                        <i class="fas fa-tasks me-2"></i> Tugas Guru
                    </a>
                </li>

                <!-- Jurnal Mengajar -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.jurnal-mengajar.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.jurnal-mengajar.index') }}">
                        <i class="fas fa-book me-2"></i> Jurnal Mengajar
                    </a>
                </li>

                <!-- Agenda Guru -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.agenda-guru.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.agenda-guru.index') }}">
                        <i class="fas fa-clipboard-list me-2"></i> Agenda Guru
                    </a>
                </li>

                <!-- Agenda Kepala Sekolah -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.agenda') && !request()->routeIs('kepala-sekolah.agenda-guru.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.agenda') }}">
                        <i class="fas fa-calendar-alt me-2"></i> Agenda Saya
                    </a>
                </li>

                <!-- Manajemen Konten -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.kegiatan-kokurikuler.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.kegiatan-kokurikuler.index') }}">
                        <i class="fas fa-running me-2"></i> Kokurikuler
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.prestasi.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.prestasi.index') }}">
                        <i class="fas fa-trophy me-2"></i> Prestasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.berita.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.berita.index') }}">
                        <i class="fas fa-newspaper me-2"></i> Berita
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.galeri.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.galeri.index') }}">
                        <i class="fas fa-images me-2"></i> Galeri Sekolah
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kepala-sekolah.saran.*') ? 'active' : '' }}" href="{{ route('kepala-sekolah.saran.index') }}">
                        <i class="fas fa-envelope me-2"></i> Kotak Saran
                    </a>
                </li>

                <!-- Data Sekolah -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('school.profile') ? 'active' : '' }}" href="{{ route('school.profile') }}">
                        <i class="fas fa-school me-2"></i> Data Sekolah
                    </a>
                </li>
            @elseif(auth()->user()->isGuru())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" href="{{ route('guru.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.absensi.siswa') ? 'active' : '' }}" href="{{ route('guru.absensi.siswa') }}">
                        <i class="fas fa-user-check me-2"></i> Absensi Siswa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.rekap-absensi.siswa*') ? 'active' : '' }}" href="{{ route('guru.rekap-absensi.siswa') }}">
                        <i class="fas fa-clipboard-check me-2"></i> Rekap Absensi Siswa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('rekap-jurnal.*') ? 'active' : '' }}" href="{{ route('rekap-jurnal.index') }}">
                        <i class="fas fa-book-open me-2"></i> Rekap Jurnal & Absensi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.absensi-guru') ? 'active' : '' }}" href="{{ route('guru.absensi-guru') }}">
                        <i class="fas fa-camera me-2"></i> Absensi Guru
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.jadwal-mengajar.today') ? 'active' : '' }}" href="{{ route('guru.jadwal-mengajar.today') }}">
                        <i class="fas fa-calendar-day me-2"></i> Jadwal Hari Ini
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.jadwal-mengajar') ? 'active' : '' }}" href="{{ route('guru.jadwal-mengajar') }}">
                        <i class="fas fa-chalkboard-teacher me-2"></i> Jadwal Mengajar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.jurnal-mengajar.*') ? 'active' : '' }}" href="{{ route('guru.jurnal-mengajar.index') }}">
                        <i class="fas fa-book me-2"></i> Jurnal Mengajar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.agenda.*') ? 'active' : '' }}" href="{{ route('guru.agenda') }}">
                        <i class="fas fa-clipboard-list me-2"></i> Agenda Guru
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.tugas-guru.*') ? 'active' : '' }}" href="{{ route('guru.tugas-guru.index') }}">
                        <i class="fas fa-tasks me-2"></i> Tugas Guru
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.materi.*') ? 'active' : '' }}" href="{{ route('guru.materi.index') }}">
                        <i class="fas fa-book-open me-2"></i> Materi Pelajaran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.kegiatan-kokurikuler.*') ? 'active' : '' }}" href="{{ route('guru.kegiatan-kokurikuler.index') }}">
                        <i class="fas fa-running me-2"></i> Kegiatan Kokurikuler
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kegiatan-kokurikuler.*') ? 'active' : '' }}" href="{{ route('kegiatan-kokurikuler.index') }}">
                        <i class="fas fa-running me-2"></i> Info Kokurikuler
                    </a>
                </li>
            @elseif(auth()->user()->isSiswa())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}" href="{{ route('siswa.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.absensi.index') ? 'active' : '' }}" href="{{ route('siswa.absensi.index') }}">
                        <i class="fas fa-user-check me-2"></i> Absensi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.jadwal-pelajaran.*') ? 'active' : '' }}" href="{{ route('siswa.jadwal-pelajaran.index') }}">
                        <i class="fas fa-calendar-alt me-2"></i> Jadwal Pelajaran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.materi.*') ? 'active' : '' }}" href="{{ route('siswa.materi.index') }}">
                        <i class="fas fa-book-reader me-2"></i> Materi Belajar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.absensi-mapel.*') ? 'active' : '' }}" href="{{ route('siswa.absensi-mapel.index') }}">
                        <i class="fas fa-clipboard-list me-2"></i> Absensi Mapel
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kegiatan-kokurikuler.*') ? 'active' : '' }}" href="{{ route('kegiatan-kokurikuler.index') }}">
                        <i class="fas fa-running me-2"></i> Info Kokurikuler
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}" href="{{ route('profile.show')}}">
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
