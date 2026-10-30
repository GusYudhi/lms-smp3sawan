@extends('layouts.app')

@section('title', 'Penilaian Kokurikuler')

@push('styles')
<style>
    .rubrik-pill-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }
    .rubrik-pill-label {
        cursor: pointer;
        user-select: none;
        padding: 0.35rem 0.65rem;
        border-radius: 50rem;
        font-size: 0.78rem;
        font-weight: 600;
        border: 1.5px solid #dee2e6;
        background-color: #fff;
        color: #495057;
        transition: all 0.15s ease-in-out;
    }
    .rubrik-radio:checked + .rubrik-pill-label[data-val="B"] {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
    }
    .rubrik-radio:checked + .rubrik-pill-label[data-val="SB"] {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
        color: #000;
    }
    .rubrik-radio:checked + .rubrik-pill-label[data-val="BSH"] {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }
    .rubrik-radio:checked + .rubrik-pill-label[data-val="SAB"] {
        background-color: #198754;
        border-color: #198754;
        color: #fff;
    }
    .history-item.active {
        background-color: #e7f1ff;
        border-color: #b6d4fe;
        color: #084298;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-star text-warning me-2"></i>Penilaian Kokurikuler Kelas {{ $kelas->full_name }}
            </h1>
            @if($activeSemester)
                <p class="text-muted mb-0">
                    <small>{{ $activeSemester->tahunPelajaran->nama ?? '' }} - {{ $activeSemester->nama }} (Jam Ke-0)</small>
                </p>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.penilaian-kokurikuler.rekap') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-1"></i> Rekap & Export Excel
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!$activeSemester)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>Belum ada semester aktif. Hubungi Administrator.
        </div>
    @else
        <!-- Ringkasan Tema Mingguan (Selasa - Jumat) -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <span class="fw-bold text-secondary small text-uppercase">
                        <i class="fas fa-calendar-week me-1"></i> Jadwal & Tema Kokurikuler Jam Ke-0 Minggu Ini:
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
                                Admin belum menambahkan Jadwal Tetap bertipe <strong>Kokurikuler</strong> pada semester ini. Anda tetap dapat menginput penilaian dengan menuliskan tema secara manual di bawah.
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
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Tanggal Pelaksanaan</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ $selectedDate }}"
                                    onchange="document.getElementById('dateFilterForm').submit()">
                            </div>
                            <small class="text-muted d-block">
                                Anda dapat memilih tanggal hari ini maupun tanggal pertemuan sebelumnya jika belum sempat menilai.
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
                            <a href="{{ route('guru.penilaian-kokurikuler.index', ['tanggal' => $histDateStr]) }}"
                               class="list-group-item list-group-item-action history-item {{ $isActiveDate ? 'active' : '' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="small">{{ $hist->hari }}, {{ \Carbon\Carbon::parse($hist->tanggal)->format('d/m/Y') }}</strong>
                                    <span class="badge bg-success">{{ $hist->total_dinilai }} siswa</span>
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

            <!-- Kolom Kanan: Form Input Nilai Siswa -->
            <div class="col-lg-9 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">
                                    Pertemuan Hari {{ $selectedDayName }}, {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }}
                                </h5>
                                @if($existingAssessments->isNotEmpty())
                                    <span class="badge bg-success mt-1">
                                        <i class="fas fa-check-circle me-1"></i>Sudah Dinilai ({{ $existingAssessments->count() }}/{{ $students->count() }} Siswa)
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark mt-1">
                                        <i class="fas fa-clock me-1"></i>Belum Dinilai
                                    </span>
                                @endif
                            </div>

                            <!-- Quick Fill Buttons -->
                            <div class="d-flex flex-wrap align-items-center gap-1">
                                <span class="small text-muted me-1">Set Semua:</span>
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
                        @if(!$kokurikulerSchedule && !in_array($selectedDayName, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']))
                            <div class="bg-warning bg-opacity-10 border border-warning text-dark p-3 rounded mb-3">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Tanggal yang dipilih adalah hari <strong>{{ $selectedDayName }}</strong> (di luar jadwal efektif sekolah).
                            </div>
                        @endif

                        <form action="{{ route('guru.penilaian-kokurikuler.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tanggal" value="{{ $selectedDate }}">

                            <div class="row g-3 align-items-center mb-3 p-3 bg-light rounded">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold mb-0">Tema Kokurikuler ({{ $selectedDayName }}):</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="tema" class="form-control fw-semibold"
                                           value="{{ $existingAssessments->first()?->tema ?? ($kokurikulerSchedule->keterangan ?? 'Kokurikuler ' . $selectedDayName) }}"
                                           placeholder="Nama Tema Kokurikuler Hari {{ $selectedDayName }}" required>
                                    @if($kokurikulerSchedule)
                                        <small class="text-muted">Otomatis diambil dari Jadwal Tetap Jam ke-{{ $kokurikulerSchedule->jam_ke }} hari {{ $selectedDayName }}.</small>
                                    @endif
                                </div>
                            </div>

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
                                            <th style="width: 240px;">Nama Siswa</th>
                                            <th>Capaian Rubrik Kokurikuler</th>
                                            <th style="width: 240px;">Catatan (Opsional)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students as $idx => $student)
                                            @php
                                                $existing = $existingAssessments->get($student->user_id);
                                                $currentVal = $existing?->predikat ?? '';
                                                $currentNote = $existing?->catatan ?? '';
                                            @endphp
                                            <tr>
                                                <td class="text-muted">{{ $idx + 1 }}</td>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $student->user->name ?? '-' }}</div>
                                                    <small class="text-muted">NIS: {{ $student->nis ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <div class="rubrik-pill-group">
                                                        @foreach(\App\Models\PenilaianKokurikuler::PREDIKAT_LABELS as $code => $label)
                                                            @php $inputId = "pred_{$student->user_id}_{$code}"; @endphp
                                                            <div>
                                                                <input type="radio"
                                                                       class="btn-check rubrik-radio"
                                                                       name="nilai[{{ $student->user_id }}][predikat]"
                                                                       id="{{ $inputId }}"
                                                                       value="{{ $code }}"
                                                                       {{ $currentVal === $code ? 'checked' : '' }}>
                                                                <label class="rubrik-pill-label" for="{{ $inputId }}" data-val="{{ $code }}" title="{{ $label }}">
                                                                    {{ $label }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                           name="nilai[{{ $student->user_id }}][catatan]"
                                                           class="form-control form-control-sm"
                                                           value="{{ $currentNote }}"
                                                           placeholder="Catatan khusus...">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    Tidak ada siswa aktif di kelas {{ $kelas->full_name }}.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($students->isNotEmpty())
                                <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Simpan Penilaian Pertemuan Ini
                                    </button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function setAllPredikat(code) {
        const radios = document.querySelectorAll(`.rubrik-radio[value="${code}"]`);
        radios.forEach(r => {
            r.checked = true;
        });
    }
</script>
@endpush
@endsection

