@extends('layouts.app')

@section('title', 'Penilaian Kokurikuler')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-star text-warning me-2"></i>Penilaian Kokurikuler Kelas {{ $kelas->full_name }}
            </h1>
            <div class="d-flex flex-wrap align-items-center gap-2">
                @if($activeSemester)
                    <p class="text-muted mb-0">
                        <small>{{ $activeSemester->tahunPelajaran->nama ?? '' }} - {{ $activeSemester->nama }} (Jam Ke-0)</small>
                    </p>
                @endif
                @if(!$canAssessRubric)
                    <span class="badge bg-info text-dark">
                        <i class="fas fa-user-friends me-1"></i> Guru Pendamping
                    </span>
                @else
                    <span class="badge bg-primary">
                        <i class="fas fa-user-tie me-1"></i> Wali Kelas
                    </span>
                @endif
            </div>

            @if(isset($accessibleClasses) && $accessibleClasses->count() > 1)
                <div class="mt-2 d-flex align-items-center gap-2">
                    <span class="small fw-semibold text-secondary">Pilih Kelas Dampingan:</span>
                    <form action="{{ route('guru.penilaian-kokurikuler.index') }}" method="GET" class="d-inline">
                        <input type="hidden" name="tanggal" value="{{ $selectedDate }}">
                        <select name="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto; min-width: 140px;">
                            @foreach($accessibleClasses as $ac)
                                <option value="{{ $ac->id }}" {{ $kelas->id === $ac->id ? 'selected' : '' }}>
                                    Kelas {{ $ac->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif
        </div>
        @if(auth()->user()->isWaliKelas())
        <div class="d-flex gap-2">
            <a href="{{ route('guru.penilaian-kokurikuler.rekap') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-1"></i> Rekap & Export Excel
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!$activeSemester)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>Belum ada semester aktif. Hubungi Administrator.
        </div>
    @else
        <!-- Ringkasan Tema Mingguan -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <span class="fw-bold text-secondary small text-uppercase">
                        <i class="fas fa-calendar-week me-1"></i> Jadwal & Tema Kokurikuler / Pembiasaan Jam Ke-0 Minggu Ini:
                    </span>
                </div>
                <div class="row g-2">
                    @forelse($allKokurikulerSchedules as $sched)
                        <div class="col-md-3 col-sm-6">
                            <div class="p-2 rounded border {{ $sched->hari === $selectedDayName ? 'bg-primary bg-opacity-10 border-primary' : 'bg-light' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold {{ $sched->hari === $selectedDayName ? 'text-primary' : 'text-dark' }}">
                                        {{ $sched->hari }}
                                    </span>
                                    <span class="badge bg-secondary">Jam ke-{{ $sched->jam_ke }}</span>
                                </div>
                                <div class="small text-muted text-truncate mt-1" title="{{ $sched->keterangan }}">
                                    {{ $sched->keterangan }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="bg-warning bg-opacity-10 text-dark p-2 rounded small border border-warning">
                                <i class="fas fa-info-circle me-1"></i>
                                Admin belum menambahkan Jadwal Tetap bertipe <strong>Kokurikuler / Pembiasaan</strong> pada semester ini. Anda tetap dapat menginput penilaian dengan menuliskan tema secara manual di bawah.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Kolom Kiri: Pilih Tanggal & Riwayat Pertemuan -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-calendar-alt me-1"></i> Pilih Tanggal Pertemuan
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('guru.penilaian-kokurikuler.index') }}" method="GET" id="dateFilterForm">
                            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Tanggal Pelaksanaan</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ $selectedDate }}"
                                    onchange="document.getElementById('dateFilterForm').submit()">
                            </div>
                            <small class="text-muted d-block">
                                Pilih tanggal hari ini atau tanggal pertemuan sebelumnya jika belum sempat mengisi.
                            </small>
                        </form>
                    </div>
                </div>

                <!-- Riwayat Pertemuan yang Sudah Dinilai -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-history me-1"></i> Riwayat Pertemuan
                        </h6>
                        <span class="badge bg-primary rounded-pill">{{ $historySessions->count() }}</span>
                    </div>
                    <div class="list-group list-group-flush" style="max-height: 380px; overflow-y: auto;">
                        @forelse($historySessions as $hist)
                            @php
                                $histDateStr = \Carbon\Carbon::parse($hist->tanggal)->format('Y-m-d');
                                $isActiveDate = ($histDateStr === $selectedDate);
                            @endphp
                            <a href="{{ route('guru.penilaian-kokurikuler.index', ['tanggal' => $histDateStr, 'kelas_id' => $kelas->id]) }}"
                               class="list-group-item list-group-item-action history-item {{ $isActiveDate ? 'active' : '' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="small">{{ $hist->hari }}, {{ \Carbon\Carbon::parse($hist->tanggal)->format('d/m/Y') }}</strong>
                                    <span class="badge bg-success">{{ $hist->total_dinilai }}/{{ $hist->total_siswa }} dinilai</span>
                                </div>
                                <div class="small text-muted text-truncate mt-1">{{ $hist->tema }}</div>
                            </a>
                        @empty
                            <div class="p-3 text-center text-muted small">
                                Belum ada riwayat pertemuan yang dinilai pada semester ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Wizard 3 Langkah -->
            <div class="col-lg-9 mb-4">
                <!-- Header Pertemuan & Indikator Step -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                Pertemuan Hari {{ $selectedDayName }}, {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }}
                            </h5>
                            <small class="text-muted">
                                Tema: <strong>{{ $existingSession?->tema ?? ($existingAssessments->first()?->tema ?? ($kokurikulerSchedule->keterangan ?? 'Kokurikuler ' . $selectedDayName)) }}</strong>
                            </small>
                        </div>

                        @php
                            $savedAttendanceCount = $existingAssessments->whereNotNull('status_absensi')->count();
                            $totalStudents = $students->count();
                            $isAbsensiFilled = $savedAttendanceCount > 0;

                            $hasFotoBukti = !empty($existingSession?->foto_bukti);
                            $isDokumentasiFilled = $hasFotoBukti;

                            $presentAssessments = $existingAssessments->filter(fn($a) => in_array($a->status_absensi, ['hadir', 'terlambat'], true));
                            $totalHadir = $presentAssessments->count();
                            $gradedCount = $presentAssessments->whereNotNull('predikat')->where('predikat', '!=', '')->count();
                            $isRubrikFilled = ($totalHadir > 0 && $gradedCount === $totalHadir);
                            $isRubrikPartial = ($totalHadir > 0 && $gradedCount > 0 && $gradedCount < $totalHadir);
                        @endphp

                        <!-- Step Indicator -->
                        <div class="wizard-step-nav">
                            <div class="wizard-step-badge active" id="indicator-step-1" onclick="goToStep(1)">
                                <span class="step-title"><i class="fas fa-user-check"></i> 1. Absensi Siswa</span>
                                @if($isAbsensiFilled)
                                    <span class="step-status-pill status-done">
                                        <i class="fas fa-check-circle"></i> Sudah ({{ $savedAttendanceCount }}/{{ $totalStudents }})
                                    </span>
                                @else
                                    <span class="step-status-pill status-pending">
                                        <i class="fas fa-clock"></i> Belum Diisi
                                    </span>
                                @endif
                            </div>

                            <div class="wizard-step-badge" id="indicator-step-2" onclick="goToStep(2)">
                                <span class="step-title"><i class="fas fa-camera"></i> 2. Dokumentasi Kegiatan</span>
                                @if($isDokumentasiFilled)
                                    <span class="step-status-pill status-done">
                                        <i class="fas fa-check-circle"></i> Foto Bukti Ada
                                    </span>
                                @else
                                    <span class="step-status-pill status-pending">
                                        <i class="fas fa-clock"></i> Belum Ada Foto
                                    </span>
                                @endif
                            </div>

                            @if($canAssessRubric)
                            <div class="wizard-step-badge" id="indicator-step-3" onclick="goToStep(3)">
                                <span class="step-title"><i class="fas fa-star"></i> 3. Penilaian Rubrik</span>
                                @if($isRubrikFilled)
                                    <span class="step-status-pill status-done">
                                        <i class="fas fa-check-circle"></i> Lengkap ({{ $gradedCount }}/{{ $totalHadir }})
                                    </span>
                                @elseif($isRubrikPartial)
                                    <span class="step-status-pill status-partial">
                                        <i class="fas fa-adjust"></i> {{ $gradedCount }}/{{ $totalHadir }} Dinilai
                                    </span>
                                @else
                                    <span class="step-status-pill status-pending">
                                        <i class="fas fa-clock"></i> Belum Dinilai
                                    </span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!$kokurikulerSchedule && !in_array($selectedDayName, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']))
                    <div class="bg-warning bg-opacity-10 border border-warning text-dark p-3 rounded mb-3">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        Tanggal yang dipilih adalah hari <strong>{{ $selectedDayName }}</strong> (di luar jadwal efektif sekolah).
                    </div>
                @endif

                <form action="{{ route('guru.penilaian-kokurikuler.store') }}" method="POST" id="formKokurikulerWizard">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                    <input type="hidden" name="tanggal" value="{{ $selectedDate }}">
                    <input type="hidden" name="foto_bukti" id="foto_bukti" value="">

                    <!-- ========================================================= -->
                    <!-- STEP 1: ABSENSI SISWA                                     -->
                    <!-- ========================================================= -->
                    <div class="wizard-step" id="step-1">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Step 1: Absensi Siswa</h5>
                            </div>
                            <div class="card-body">
                                <div class="bg-info bg-opacity-10 border border-info text-dark p-3 rounded mb-3 small">
                                    <i class="fas fa-info-circle me-1 text-primary"></i>
                                    Silakan periksa kehadiran siswa untuk kegiatan kokurikuler ini. Status awal otomatis mengikuti <strong>Absensi Pagi</strong> hari tersebut. Siswa yang <strong>Sakit / Izin / Alpa</strong> otomatis tidak dinilai rubriknya pada Step 3.
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th style="width: 120px;">NIS</th>
                                                <th>Nama Siswa</th>
                                                <th style="width: 150px;">Absensi Pagi</th>
                                                <th style="width: 180px;">Status Absensi Kokurikuler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($students as $idx => $student)
                                                @php
                                                    $absensiHarian = $absensiPagi->get($student->user_id);
                                                    $statusPagi = $absensiHarian ? $absensiHarian->status : 'belum_absen';
                                                    $existing = $existingAssessments->get($student->user_id);

                                                    if ($existing && $existing->status_absensi) {
                                                        $selectedStatus = $existing->status_absensi;
                                                    } elseif (in_array($statusPagi, ['sakit', 'izin', 'alpa'])) {
                                                        $selectedStatus = $statusPagi;
                                                    } else {
                                                        $selectedStatus = 'hadir';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="text-muted">{{ $idx + 1 }}</td>
                                                    <td>{{ $student->nis ?? '-' }}</td>
                                                    <td class="fw-semibold text-dark">{{ $student->user->name ?? '-' }}</td>
                                                    <td>
                                                        @if($statusPagi === 'hadir')
                                                            <span class="badge bg-success">Hadir</span>
                                                        @elseif($statusPagi === 'terlambat')
                                                            <span class="badge bg-warning text-dark">Terlambat</span>
                                                        @elseif($statusPagi === 'sakit')
                                                            <span class="badge bg-warning text-dark">Sakit</span>
                                                        @elseif($statusPagi === 'izin')
                                                            <span class="badge bg-info text-dark">Izin</span>
                                                        @elseif($statusPagi === 'alpa')
                                                            <span class="badge bg-danger">Alpa</span>
                                                        @else
                                                            <span class="badge bg-secondary">Belum Absen</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm absensi-select"
                                                                name="absensi[{{ $student->user_id }}]"
                                                                data-student-id="{{ $student->user_id }}"
                                                                onchange="syncAttendanceToRubric()"
                                                                required>
                                                            <option value="hadir" {{ $selectedStatus === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                            <option value="terlambat" {{ $selectedStatus === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                                            <option value="sakit" {{ $selectedStatus === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                            <option value="izin" {{ $selectedStatus === 'izin' ? 'selected' : '' }}>Izin</option>
                                                            <option value="alpa" {{ $selectedStatus === 'alpa' ? 'selected' : '' }}>Alpa</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        Tidak ada siswa aktif di kelas {{ $kelas->full_name }}.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($students->isNotEmpty())
                                    <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                                        <button type="button" class="btn btn-primary px-4" onclick="goToStep(2)">
                                            Lanjut ke Dokumentasi <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ========================================================= -->
                    <!-- STEP 2: DOKUMENTASI KEGIATAN (TEMA, DESKRIPSI & FOTO)     -->
                    <!-- ========================================================= -->
                    <div class="wizard-step" id="step-2" style="display: none;">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-success text-white py-3">
                                <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Step 2: Dokumentasi Kegiatan Kokurikuler</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        Tema Kokurikuler ({{ $selectedDayName }}) <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="tema"
                                           id="input_tema"
                                           class="form-control fw-semibold"
                                           value="{{ old('tema', $existingSession?->tema ?? ($existingAssessments->first()?->tema ?? ($kokurikulerSchedule->keterangan ?? 'Kokurikuler ' . $selectedDayName))) }}"
                                           placeholder="Nama Tema Kokurikuler Hari {{ $selectedDayName }}"
                                           required>
                                    @if($kokurikulerSchedule)
                                        <small class="text-muted">Otomatis diambil dari Jadwal Tetap Jam ke-{{ $kokurikulerSchedule->jam_ke }} hari {{ $selectedDayName }}.</small>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="input_deskripsi" class="form-label fw-bold">
                                        Deskripsi / Catatan Kegiatan <span class="text-muted fw-normal">(Opsional)</span>
                                    </label>
                                    <textarea class="form-control"
                                              id="input_deskripsi"
                                              name="deskripsi"
                                              rows="3"
                                              placeholder="Tuliskan deskripsi singkat pelaksanaan kegiatan kokurikuler hari ini (opsional)...">{{ old('deskripsi', $existingSession?->deskripsi ?? '') }}</textarea>
                                </div>

                                <!-- Foto Bukti Kegiatan (Wajib) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        Foto Bukti Kegiatan <span class="text-danger">*</span>
                                    </label>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <button type="button" class="btn btn-outline-primary w-100" onclick="openCamera()">
                                                <i class="fas fa-camera me-2"></i>Ambil Foto Kamera
                                            </button>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="btn btn-outline-secondary w-100 mb-0">
                                                <i class="fas fa-upload me-2"></i>Upload Foto dari Perangkat
                                                <input type="file"
                                                       id="upload_foto"
                                                       accept="image/*,.heic,.heif"
                                                       style="display: none;"
                                                       onchange="handleFileUpload(event)">
                                            </label>
                                        </div>
                                    </div>

                                    @php
                                        $hasSavedPhoto = !empty($existingSession?->foto_bukti);
                                        $savedPhotoUrl = $hasSavedPhoto ? Storage::url($existingSession->foto_bukti) : '';
                                    @endphp

                                    <div id="preview_container" style="{{ $hasSavedPhoto ? 'display: block;' : 'display: none;' }}" class="mt-3 p-3 bg-light rounded border">
                                        <p class="text-muted small mb-2 fw-semibold">
                                            <i class="fas fa-image me-1"></i>Preview Foto Bukti:
                                        </p>
                                        <img id="preview_foto"
                                             src="{{ $savedPhotoUrl }}"
                                             alt="Preview Foto Bukti"
                                             class="img-fluid rounded shadow-sm d-block"
                                             style="max-height: 300px;">
                                        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="resetFoto()">
                                            <i class="fas fa-times me-1"></i>Hapus / Ganti Foto
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-secondary" onclick="goToStep(1)">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Absensi
                                    </button>
                                    @if($canAssessRubric)
                                        <button type="button" class="btn btn-primary px-4" onclick="validateStep2AndNext()">
                                            Lanjut ke Penilaian Siswa <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success px-4" onclick="submitStep2ForPendamping()">
                                            <i class="fas fa-save me-2"></i>Simpan Absensi & Dokumentasi
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($canAssessRubric)
                    <!-- ========================================================= -->
                    <!-- STEP 3: PENILAIAN RUBRIK SISWA                            -->
                    <!-- ========================================================= -->
                    <div class="wizard-step" id="step-3" style="display: none;">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <div>
                                        <h5 class="mb-0 fw-bold text-dark">
                                            <i class="fas fa-star text-warning me-2"></i>Step 3: Penilaian Rubrik Siswa
                                        </h5>
                                        <small class="text-muted">Pilih capaian rubrik untuk siswa yang hadir pada pertemuan ini.</small>
                                    </div>

                                    <!-- Quick Fill Buttons -->
                                    <div class="d-flex flex-wrap align-items-center gap-1">
                                        <span class="small text-muted me-1">Set Semua (Hadir):</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" onclick="setAllPredikat('BSH')">
                                            Berkembang Sesuai Harapan (BSH)
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="setAllPredikat('SAB')">
                                            Sangat Berkembang (SAB)
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info text-dark" onclick="setAllPredikat('SB')">
                                            Sedang Berkembang (SB)
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setAllPredikat('B')">
                                            Berkembang (B)
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Keterangan Singkatan Rubrik -->
                                <div class="d-flex flex-wrap gap-3 mb-3 small text-muted px-1">
                                    <span><strong class="badge bg-secondary">B</strong> = Berkembang</span>
                                    <span><strong class="badge bg-info text-dark">SB</strong> = Sedang Berkembang</span>
                                    <span><strong class="badge bg-primary">BSH</strong> = Berkembang Sesuai Harapan</span>
                                    <span><strong class="badge bg-success">SAB</strong> = Sangat Berkembang</span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 45px;">No</th>
                                                <th style="width: 220px;">Nama Siswa</th>
                                                <th style="width: 110px;">Kehadiran</th>
                                                <th>Capaian Rubrik Kokurikuler</th>
                                                <th style="width: 220px;">Catatan (Opsional)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($students as $idx => $student)
                                                @php
                                                    $existing = $existingAssessments->get($student->user_id);
                                                    $currentVal = $existing?->predikat ?? '';
                                                    $currentNote = $existing?->catatan ?? '';
                                                @endphp
                                                <tr id="rubric-row-{{ $student->user_id }}">
                                                    <td class="text-muted">{{ $idx + 1 }}</td>
                                                    <td>
                                                        <div class="fw-semibold text-dark">{{ $student->user->name ?? '-' }}</div>
                                                        <small class="text-muted">NIS: {{ $student->nis ?? '-' }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success" id="attendance-badge-{{ $student->user_id }}">Hadir</span>
                                                    </td>
                                                    <td>
                                                        <div class="rubrik-pill-group" id="rubric-group-{{ $student->user_id }}">
                                                            @foreach(\App\Models\PenilaianKokurikuler::PREDIKAT_LABELS as $code => $label)
                                                                @php $inputId = "pred_{$student->user_id}_{$code}"; @endphp
                                                                <div>
                                                                    <input type="radio"
                                                                           class="btn-check rubrik-radio student-radio-{{ $student->user_id }}"
                                                                           name="nilai[{{ $student->user_id }}][predikat]"
                                                                           id="{{ $inputId }}"
                                                                           value="{{ $code }}"
                                                                           data-student-id="{{ $student->user_id }}"
                                                                           {{ $currentVal === $code ? 'checked' : '' }}>
                                                                    <label class="rubrik-pill-label" for="{{ $inputId }}" data-val="{{ $code }}" title="{{ $label }}">
                                                                        {{ $label }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div id="absent-notice-{{ $student->user_id }}" class="small text-muted fst-italic" style="display: none;">
                                                            Siswa tidak hadir — tidak dinilai rubrik.
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text"
                                                               name="nilai[{{ $student->user_id }}][catatan]"
                                                               id="note-input-{{ $student->user_id }}"
                                                               class="form-control form-control-sm"
                                                               value="{{ $currentNote }}"
                                                               placeholder="Catatan khusus...">
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        Tidak ada siswa aktif di kelas {{ $kelas->full_name }}.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($students->isNotEmpty())
                                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                        <button type="button" class="btn btn-secondary" onclick="goToStep(2)">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dokumentasi
                                        </button>
                                        <button type="submit" class="btn btn-success px-4">
                                            <i class="fas fa-save me-2"></i>Simpan Absensi & Penilaian
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    @endif
</div>

<!-- Modal Kamera -->
<div class="modal fade" id="modalKamera" tabindex="-1" aria-labelledby="modalKameraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKameraLabel">Ambil Foto Bukti Kokurikuler</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <video id="video" width="100%" autoplay style="border-radius: 8px;"></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <div id="captured-image" style="display: none;">
                    <img id="captured-img" src="" alt="Captured" style="width: 100%; border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn-capture" onclick="capturePhoto()">
                    <i class="fas fa-camera me-2"></i>Ambil Foto
                </button>
                <button type="button" class="btn btn-warning" id="btn-retake" style="display: none;" onclick="retakePhoto()">
                    <i class="fas fa-redo me-2"></i>Foto Ulang
                </button>
                <button type="button" class="btn btn-success" id="btn-use-photo" style="display: none;" onclick="usePhoto()">
                    <i class="fas fa-check me-2"></i>Gunakan Foto Ini
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentStream = null;
    let capturedImageData = null;
    let hasExistingSavedPhoto = @json(!empty($existingSession?->foto_bukti));
    const canAssessRubricGlobal = @json($canAssessRubric);

    function hasValidPhoto() {
        const base64Val = document.getElementById('foto_bukti')?.value || '';
        return base64Val.length > 0 || hasExistingSavedPhoto;
    }

    function goToStep(stepNumber) {
        if (stepNumber === 3 && !canAssessRubricGlobal) {
            return;
        }

        if (stepNumber === 3 && !hasValidPhoto()) {
            Swal.fire({
                icon: 'warning',
                title: 'Foto Bukti Wajib Diisi',
                text: 'Silakan ambil atau upload foto bukti kegiatan kokurikuler pada Step 2 terlebih dahulu.'
            });
            stepNumber = 2;
        }

        [1, 2, 3].forEach(num => {
            const stepEl = document.getElementById(`step-${num}`);
            const indEl = document.getElementById(`indicator-step-${num}`);
            if (stepEl) {
                stepEl.style.display = (num === stepNumber) ? 'block' : 'none';
            }
            if (indEl) {
                indEl.classList.remove('active', 'completed');
                if (num === stepNumber) {
                    indEl.classList.add('active');
                } else if (num < stepNumber) {
                    indEl.classList.add('completed');
                }
            }
        });

        if (stepNumber === 3) {
            syncAttendanceToRubric();
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function submitStep2ForPendamping() {
        const temaInput = document.getElementById('input_tema');
        if (temaInput && !temaInput.value.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Tema Belum Diisi',
                text: 'Silakan isi nama Tema Kokurikuler terlebih dahulu.'
            });
            return;
        }

        if (!hasValidPhoto()) {
            Swal.fire({
                icon: 'warning',
                title: 'Foto Bukti Wajib Diisi',
                text: 'Silakan ambil foto menggunakan kamera atau upload foto bukti kegiatan terlebih dahulu.'
            });
            return;
        }

        document.getElementById('formKokurikulerWizard').submit();
    }

    function validateStep2AndNext() {
        const temaInput = document.getElementById('input_tema');
        if (temaInput && !temaInput.value.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Tema Belum Diisi',
                text: 'Silakan isi nama Tema Kokurikuler terlebih dahulu.'
            });
            return;
        }

        if (!hasValidPhoto()) {
            Swal.fire({
                icon: 'warning',
                title: 'Foto Bukti Wajib Diisi',
                text: 'Silakan ambil foto menggunakan kamera atau upload foto bukti kegiatan terlebih dahulu.'
            });
            return;
        }

        goToStep(3);
    }

    function syncAttendanceToRubric() {
        const selects = document.querySelectorAll('.absensi-select');
        const badgeMap = {
            hadir: { text: 'Hadir', cls: 'bg-success' },
            terlambat: { text: 'Terlambat', cls: 'bg-warning text-dark' },
            sakit: { text: 'Sakit', cls: 'bg-warning text-dark' },
            izin: { text: 'Izin', cls: 'bg-info text-dark' },
            alpa: { text: 'Alpa', cls: 'bg-danger' }
        };

        selects.forEach(select => {
            const studentId = select.getAttribute('data-student-id');
            const status = select.value;
            const isPresent = (status === 'hadir' || status === 'terlambat');

            const badgeEl = document.getElementById(`attendance-badge-${studentId}`);
            if (badgeEl && badgeMap[status]) {
                badgeEl.className = `badge ${badgeMap[status].cls}`;
                badgeEl.textContent = badgeMap[status].text;
            }

            const radios = document.querySelectorAll(`.student-radio-${studentId}`);
            const rubricGroup = document.getElementById(`rubric-group-${studentId}`);
            const absentNotice = document.getElementById(`absent-notice-${studentId}`);

            radios.forEach(r => {
                r.disabled = !isPresent;
                if (!isPresent) {
                    r.checked = false;
                }
            });

            if (rubricGroup) {
                rubricGroup.style.display = isPresent ? 'flex' : 'none';
            }
            if (absentNotice) {
                absentNotice.style.display = isPresent ? 'none' : 'block';
            }
        });
    }

    function setAllPredikat(code) {
        syncAttendanceToRubric();
        const radios = document.querySelectorAll(`.rubrik-radio[value="${code}"]`);
        radios.forEach(r => {
            if (!r.disabled) {
                r.checked = true;
            }
        });
    }

    // Camera & Photo Compression functions
    function openCamera() {
        const modal = new bootstrap.Modal(document.getElementById('modalKamera'));
        modal.show();

        const video = document.getElementById('video');

        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        })
        .then(stream => {
            currentStream = stream;
            video.srcObject = stream;
        })
        .catch(err => {
            console.error('Error accessing camera:', err);
            Swal.fire({
                icon: 'error',
                title: 'Kamera Tidak Dapat Diakses',
                text: 'Pastikan Anda memberikan izin akses kamera pada browser.'
            });
        });

        document.getElementById('modalKamera').addEventListener('hidden.bs.modal', function () {
            stopCamera();
        });
    }

    function compressImage(sourceCanvas, maxWidth = 1200, quality = 0.6) {
        let width = sourceCanvas.width;
        let height = sourceCanvas.height;

        if (width > maxWidth) {
            height = Math.round((height * maxWidth) / width);
            width = maxWidth;
        }

        const compressCanvas = document.createElement('canvas');
        compressCanvas.width = width;
        compressCanvas.height = height;
        const compressContext = compressCanvas.getContext('2d');
        compressContext.drawImage(sourceCanvas, 0, 0, width, height);

        let compressedData = compressCanvas.toDataURL('image/webp', quality);
        if (!compressedData.startsWith('data:image/webp')) {
            compressedData = compressCanvas.toDataURL('image/jpeg', quality);
        }

        return compressedData;
    }

    function capturePhoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        capturedImageData = compressImage(canvas, 1200, 0.6);

        document.getElementById('captured-img').src = capturedImageData;
        video.style.display = 'none';
        document.getElementById('captured-image').style.display = 'block';
        document.getElementById('btn-capture').style.display = 'none';
        document.getElementById('btn-use-photo').style.display = 'inline-block';
        document.getElementById('btn-retake').style.display = 'inline-block';

        stopCamera();
    }

    function retakePhoto() {
        const video = document.getElementById('video');
        video.style.display = 'block';
        document.getElementById('captured-image').style.display = 'none';
        document.getElementById('btn-capture').style.display = 'inline-block';
        document.getElementById('btn-use-photo').style.display = 'none';
        document.getElementById('btn-retake').style.display = 'none';
        capturedImageData = null;

        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        })
        .then(stream => {
            currentStream = stream;
            video.srcObject = stream;
        });
    }

    function usePhoto() {
        if (capturedImageData) {
            document.getElementById('foto_bukti').value = capturedImageData;
            document.getElementById('preview_foto').src = capturedImageData;
            document.getElementById('preview_container').style.display = 'block';

            bootstrap.Modal.getInstance(document.getElementById('modalKamera')).hide();
        }
    }

    function stopCamera() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
    }

    function handleFileUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        const fileExtension = file.name.split('.').pop().toLowerCase();
        const isHEIC = fileExtension === 'heic' || fileExtension === 'heif';

        if (isHEIC) {
            Swal.fire({
                title: 'Memproses foto HEIC...',
                text: 'Mohon tunggu, foto sedang dikonversi dan dikompresi.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            convertAndCompressHEIC(file);
        } else {
            compressImageFile(file);
        }
    }

    function compressImageFile(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                const maxWidth = 1200;

                if (width > maxWidth) {
                    height = Math.round((height * maxWidth) / width);
                    width = maxWidth;
                }

                canvas.width = width;
                canvas.height = height;
                const context = canvas.getContext('2d');
                context.drawImage(img, 0, 0, width, height);

                let compressedData = canvas.toDataURL('image/webp', 0.6);
                if (!compressedData.startsWith('data:image/webp')) {
                    compressedData = canvas.toDataURL('image/jpeg', 0.6);
                }

                document.getElementById('foto_bukti').value = compressedData;
                document.getElementById('preview_foto').src = compressedData;
                document.getElementById('preview_container').style.display = 'block';
            };
            img.onerror = function() {
                Swal.fire('Gagal', 'Gagal memuat foto. Pastikan format file didukung.', 'error');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function convertAndCompressHEIC(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("convert-heic") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.image) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    const maxWidth = 1200;

                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const context = canvas.getContext('2d');
                    context.drawImage(img, 0, 0, width, height);

                    let compressedData = canvas.toDataURL('image/webp', 0.6);
                    if (!compressedData.startsWith('data:image/webp')) {
                        compressedData = canvas.toDataURL('image/jpeg', 0.6);
                    }

                    document.getElementById('foto_bukti').value = compressedData;
                    document.getElementById('preview_foto').src = compressedData;
                    document.getElementById('preview_container').style.display = 'block';
                    Swal.close();
                };
                img.src = data.image;
            } else {
                Swal.fire('Gagal', data.message || 'Gagal mengkonversi foto HEIC.', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Gagal', 'Terjadi kesalahan saat mengkonversi foto HEIC.', 'error');
        });
    }

    function resetFoto() {
        hasExistingSavedPhoto = false;
        document.getElementById('foto_bukti').value = '';
        document.getElementById('preview_foto').src = '';
        document.getElementById('preview_container').style.display = 'none';
        const uploadInput = document.getElementById('upload_foto');
        if (uploadInput) uploadInput.value = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        syncAttendanceToRubric();

        const form = document.getElementById('formKokurikulerWizard');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!hasValidPhoto()) {
                    e.preventDefault();
                    goToStep(2);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Foto Bukti Wajib Diisi',
                        text: 'Silakan ambil atau upload foto bukti kegiatan terlebih dahulu!'
                    });
                    return false;
                }
            });
        }
    });
</script>
@endpush
@endsection
