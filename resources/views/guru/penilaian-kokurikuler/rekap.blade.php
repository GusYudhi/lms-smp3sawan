@extends('layouts.app')

@section('title', 'Rekap Penilaian Kokurikuler')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-chart-bar text-primary me-2"></i>Rekap Penilaian Kokurikuler
                @if($kelas)
                    — Kelas {{ $kelas->full_name }}
                @endif
            </h1>
            @if($activeSemester)
                <p class="text-muted mb-0">
                    <small>{{ $activeSemester->tahunPelajaran->nama ?? '' }} - {{ $activeSemester->nama }}</small>
                </p>
            @endif
        </div>

        <div class="d-flex flex-wrap gap-2">
            @if(auth()->user()->isWaliKelas())
                <a href="{{ route('guru.penilaian-kokurikuler.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Input Harian
                </a>
            @endif

            @if($kelas && $activeSemester)
                @php
                    $exportRoute = auth()->user()->isAdmin()
                        ? route('admin.penilaian-kokurikuler.export', ['kelas_id' => $kelas->id, 'semester_id' => $activeSemester->id])
                        : (auth()->user()->isKepalaSekolah()
                            ? route('kepala-sekolah.penilaian-kokurikuler.export', ['kelas_id' => $kelas->id, 'semester_id' => $activeSemester->id])
                            : route('guru.penilaian-kokurikuler.export', ['semester_id' => $activeSemester->id]));
                @endphp
                <a href="{{ $exportRoute }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Semester & Kelas (untuk Admin / Kepala Sekolah / Wali Kelas) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Semester</label>
                    <select name="semester_id" class="form-select" onchange="this.form.submit()">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ ($activeSemester && $activeSemester->id === $sem->id) ? 'selected' : '' }}>
                                {{ $sem->tahunPelajaran->nama ?? '' }} - {{ $sem->nama }} {{ $sem->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(auth()->user()->isAdmin() || auth()->user()->isKepalaSekolah())
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Pilih Kelas</label>
                        <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ ($kelas && $kelas->id === $k->id) ? 'selected' : '' }}>
                                    Kelas {{ $k->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if(!$kelas || !$activeSemester)
        <div class="alert alert-info">Silakan pilih kelas dan semester terlebih dahulu.</div>
    @else
        <!-- Legenda Rubrik -->
        <div class="d-flex flex-wrap gap-3 mb-3 small text-muted">
            <span><strong class="badge bg-secondary">B</strong> = Berkembang</span>
            <span><strong class="badge bg-info text-dark">SB</strong> = Sedang Berkembang</span>
            <span><strong class="badge bg-primary">BSH</strong> = Berkembang Sesuai Harapan</span>
            <span><strong class="badge bg-success">SAB</strong> = Sangat Berkembang</span>
        </div>

        <!-- Tabs: Ringkasan per Tema Hari vs Rincian Pertemuan Harian -->
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-rekap-tema" type="button" role="tab">
                    <i class="fas fa-layer-group me-1"></i> Ringkasan Capaian per Hari/Tema
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-rincian-harian" type="button" role="tab">
                    <i class="fas fa-calendar-check me-1"></i> Matriks Pertemuan Harian ({{ $sessions->count() }} Pertemuan)
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- TAB 1: Ringkasan per Tema -->
            <div class="tab-pane fade show active" id="tab-rekap-tema" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        @php
                            $daysList = $kokurikulerSchedules->isNotEmpty()
                                ? $kokurikulerSchedules
                                : collect([
                                    (object)['hari' => 'Senin', 'keterangan' => 'Pembiasaan Upacara Bendera'],
                                    (object)['hari' => 'Selasa', 'keterangan' => 'Tema Selasa'],
                                    (object)['hari' => 'Rabu', 'keterangan' => 'Tema Rabu'],
                                    (object)['hari' => 'Kamis', 'keterangan' => 'Tema Kamis'],
                                    (object)['hari' => 'Jumat', 'keterangan' => 'Tema Jumat'],
                                ]);
                            $weights = ['B' => 1, 'SB' => 2, 'BSH' => 3, 'SAB' => 4];
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th style="width: 110px;">NIS</th>
                                        <th class="text-start">Nama Siswa</th>
                                        @foreach($daysList as $d)
                                            <th>
                                                <div>{{ $d->hari }}</div>
                                                <small class="text-muted fw-normal">{{ $d->keterangan }}</small>
                                            </th>
                                        @endforeach
                                        <th style="width: 130px;">Total Dinilai</th>
                                        <th style="width: 180px;">Capaian Dominan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $idx => $student)
                                        @php
                                            $studentScores = $penilaians->where('student_user_id', $student->user_id);
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $idx + 1 }}</td>
                                            <td class="text-center">{{ $student->nis ?? '-' }}</td>
                                            <td class="fw-semibold">{{ $student->user->name ?? '-' }}</td>
                                            @foreach($daysList as $d)
                                                @php
                                                    $dayScores = $studentScores->where('hari', $d->hari);
                                                    $dom = null;
                                                    if ($dayScores->isNotEmpty()) {
                                                        $counts = $dayScores->groupBy('predikat')->map->count();
                                                        $maxC = -1;
                                                        foreach ($counts as $p => $c) {
                                                            if ($c > $maxC || ($c === $maxC && ($weights[$p] ?? 0) > ($weights[$dom] ?? 0))) {
                                                                $maxC = $c;
                                                                $dom = $p;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <td class="text-center">
                                                    @if($dom)
                                                        <span class="badge bg-{{ \App\Models\PenilaianKokurikuler::PREDIKAT_BADGES[$dom] ?? 'secondary' }}"
                                                              title="{{ \App\Models\PenilaianKokurikuler::PREDIKAT_LABELS[$dom] ?? $dom }}">
                                                            {{ $dom }}
                                                        </span>
                                                        <small class="text-muted d-block mt-1">{{ $dayScores->count() }}x pertemuan</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-center fw-bold">{{ $studentScores->count() }}x</td>
                                            <td class="text-center">
                                                @if($studentScores->isNotEmpty())
                                                    @php
                                                        $allCounts = $studentScores->groupBy('predikat')->map->count();
                                                        $overallDom = 'BSH';
                                                        $maxAll = -1;
                                                        foreach ($allCounts as $p => $c) {
                                                            if ($c > $maxAll || ($c === $maxAll && ($weights[$p] ?? 0) > ($weights[$overallDom] ?? 0))) {
                                                                $maxAll = $c;
                                                                $overallDom = $p;
                                                            }
                                                        }
                                                    @endphp
                                                    <span class="badge bg-{{ \App\Models\PenilaianKokurikuler::PREDIKAT_BADGES[$overallDom] ?? 'secondary' }}">
                                                        {{ \App\Models\PenilaianKokurikuler::PREDIKAT_LABELS[$overallDom] ?? $overallDom }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">Tidak ada data siswa.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Rincian Harian -->
            <div class="tab-pane fade" id="tab-rincian-harian" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        @if($sessions->isEmpty())
                            <div class="text-center text-muted py-4">
                                Belum ada pertemuan kokurikuler yang dinilai pada semester ini.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th class="text-start" style="min-width: 220px;">Nama Siswa</th>
                                            @foreach($sessions as $sess)
                                                <th style="min-width: 130px;">
                                                    <div>{{ $sess['hari'] }}, {{ \Carbon\Carbon::parse($sess['tanggal'])->format('d/m') }}</div>
                                                    <small class="text-muted fw-normal d-block text-truncate" style="max-width: 130px;" title="{{ $sess['tema'] }}">
                                                        {{ $sess['tema'] }}
                                                    </small>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $idx => $student)
                                            @php
                                                $byDate = $penilaians
                                                    ->where('student_user_id', $student->user_id)
                                                    ->keyBy(fn ($item) => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'));
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $idx + 1 }}</td>
                                                <td class="fw-semibold">{{ $student->user->name ?? '-' }}</td>
                                                @foreach($sessions as $sess)
                                                    @php $sc = $byDate->get($sess['tanggal']); @endphp
                                                    <td class="text-center">
                                                        @if($sc)
                                                            <span class="badge bg-{{ $sc->predikat_badge }}" title="{{ $sc->predikat_label }}">
                                                                {{ $sc->predikat }}
                                                            </span>
                                                            @if($sc->catatan)
                                                                <small class="d-block text-muted mt-1" style="font-size: 0.72rem;">{{ $sc->catatan }}</small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

