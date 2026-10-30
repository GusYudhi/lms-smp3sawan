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
                    <small>
                        {{ $activeSemester->tahunPelajaran->nama ?? '' }} - {{ $activeSemester->nama }}
                        @if(($selectedBulan ?? 'all') !== 'all' && isset($monthsList[(int) $selectedBulan]))
                            | Bulan: <strong>{{ $monthsList[(int) $selectedBulan] }}</strong>
                        @endif
                        @if(($selectedHari ?? 'all') !== 'all')
                            | Hari: <strong>{{ $selectedHari }}</strong>
                        @endif
                    </small>
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
                    $exportParams = [
                        'semester_id' => $activeSemester->id,
                        'bulan' => $selectedBulan ?? 'all',
                        'hari' => $selectedHari ?? 'all',
                    ];
                    if (auth()->user()->isAdmin() || auth()->user()->isKepalaSekolah()) {
                        $exportParams['kelas_id'] = $kelas->id;
                    }
                    $exportRoute = auth()->user()->isAdmin()
                        ? route('admin.penilaian-kokurikuler.export', $exportParams)
                        : (auth()->user()->isKepalaSekolah()
                            ? route('kepala-sekolah.penilaian-kokurikuler.export', $exportParams)
                            : route('guru.penilaian-kokurikuler.export', $exportParams));
                @endphp
                <a href="{{ $exportRoute }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Semester, Kelas, Bulan, & Hari/Tema -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="{{ (auth()->user()->isAdmin() || auth()->user()->isKepalaSekolah()) ? 'col-md-3' : 'col-md-4' }}">
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
                    <div class="col-md-3">
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

                <div class="{{ (auth()->user()->isAdmin() || auth()->user()->isKepalaSekolah()) ? 'col-md-3' : 'col-md-4' }}">
                    <label class="form-label fw-semibold small">Filter Bulan</label>
                    <select name="bulan" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ ($selectedBulan ?? 'all') === 'all' ? 'selected' : '' }}>Semua Bulan (1 Semester)</option>
                        @foreach($monthsList ?? [] as $mNum => $mName)
                            <option value="{{ $mNum }}" {{ (string)($selectedBulan ?? 'all') === (string)$mNum ? 'selected' : '' }}>
                                {{ $mName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="{{ (auth()->user()->isAdmin() || auth()->user()->isKepalaSekolah()) ? 'col-md-3' : 'col-md-4' }}">
                    <label class="form-label fw-semibold small">Filter Hari / Tema</label>
                    @php
                        $filterDays = ($allKokurikulerSchedules ?? collect())->isNotEmpty()
                            ? $allKokurikulerSchedules
                            : collect([
                                (object)['hari' => 'Senin', 'keterangan' => 'Pembiasaan Upacara Bendera'],
                                (object)['hari' => 'Selasa', 'keterangan' => 'Tema Selasa'],
                                (object)['hari' => 'Rabu', 'keterangan' => 'Tema Rabu'],
                                (object)['hari' => 'Kamis', 'keterangan' => 'Tema Kamis'],
                                (object)['hari' => 'Jumat', 'keterangan' => 'Tema Jumat'],
                            ]);
                    @endphp
                    <select name="hari" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ ($selectedHari ?? 'all') === 'all' ? 'selected' : '' }}>Semua Hari &amp; Tema</option>
                        @foreach($filterDays as $fd)
                            <option value="{{ $fd->hari }}" {{ ($selectedHari ?? 'all') === $fd->hari ? 'selected' : '' }}>
                                {{ $fd->hari }} — {{ $fd->keterangan }}
                            </option>
                        @endforeach
                    </select>
                </div>
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
                            $defaultDays = collect([
                                (object)['hari' => 'Senin', 'keterangan' => 'Pembiasaan Upacara Bendera'],
                                (object)['hari' => 'Selasa', 'keterangan' => 'Tema Selasa'],
                                (object)['hari' => 'Rabu', 'keterangan' => 'Tema Rabu'],
                                (object)['hari' => 'Kamis', 'keterangan' => 'Tema Kamis'],
                                (object)['hari' => 'Jumat', 'keterangan' => 'Tema Jumat'],
                            ]);
                            if (($selectedHari ?? 'all') !== 'all') {
                                $defaultDays = $defaultDays->where('hari', $selectedHari)->values();
                            }
                            $daysList = $kokurikulerSchedules->isNotEmpty()
                                ? $kokurikulerSchedules
                                : $defaultDays;
                            $weights = ['B' => 1, 'SB' => 2, 'BSH' => 3, 'SAB' => 4];
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle table-kokurikuler-rekap">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th class="col-sticky-no">No</th>
                                        <th class="text-start col-sticky-nama">Nama Siswa</th>
                                        @foreach($daysList as $d)
                                            <th class="col-tema-header">
                                                <div>{{ $d->hari }}</div>
                                                <small class="text-muted fw-normal d-block">{{ $d->keterangan }}</small>
                                            </th>
                                        @endforeach
                                        <th>Total Dinilai</th>
                                        <th>Capaian Dominan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $idx => $student)
                                        @php
                                            $studentScores = $penilaians
                                                ->where('student_user_id', $student->user_id)
                                                ->whereNotNull('predikat')
                                                ->where('predikat', '!=', '');
                                        @endphp
                                        <tr>
                                            <td class="text-center col-sticky-no">{{ $idx + 1 }}</td>
                                            <td class="col-sticky-nama">
                                                <div class="fw-semibold text-dark">{{ $student->user->name ?? '-' }}</div>
                                                <small class="text-muted">NIS: {{ $student->nis ?? '-' }}</small>
                                            </td>
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
                                            <td colspan="{{ 4 + $daysList->count() }}" class="text-center text-muted py-4">Tidak ada data siswa.</td>
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
                                Belum ada pertemuan kokurikuler yang dinilai pada filter periode ini.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle table-kokurikuler-rekap">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th class="col-sticky-no">No</th>
                                            <th class="text-start col-sticky-nama">Nama Siswa</th>
                                            @foreach($sessions as $sess)
                                                <th class="col-session-header">
                                                    <div>{{ $sess['hari'] }}, {{ \Carbon\Carbon::parse($sess['tanggal'])->format('d/m') }}</div>
                                                    <small class="text-muted fw-normal d-block text-truncate mx-auto session-tema-label" title="{{ $sess['tema'] }}">
                                                        {{ $sess['tema'] }}
                                                    </small>
                                                    @if(!empty($sess['foto_bukti']) || !empty($sess['deskripsi']))
                                                        <button type="button"
                                                                class="btn btn-link btn-sm p-0 mt-1 text-decoration-none session-bukti-btn"
                                                                onclick="showSessionDetail(@js($sess['hari'] . ', ' . \Carbon\Carbon::parse($sess['tanggal'])->format('d/m/Y')), @js($sess['tema']), @js($sess['deskripsi'] ?? '-'), @js(!empty($sess['foto_bukti']) ? Storage::url($sess['foto_bukti']) : ''))">
                                                            <i class="fas fa-camera me-1"></i>Bukti
                                                        </button>
                                                    @endif
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
                                                <td class="text-center col-sticky-no">{{ $idx + 1 }}</td>
                                                <td class="col-sticky-nama">
                                                    <div class="fw-semibold text-dark">{{ $student->user->name ?? '-' }}</div>
                                                    <small class="text-muted">NIS: {{ $student->nis ?? '-' }}</small>
                                                </td>
                                                @foreach($sessions as $sess)
                                                    @php $sc = $byDate->get($sess['tanggal']); @endphp
                                                    <td class="text-center">
                                                        @if($sc)
                                                            <span class="badge bg-{{ $sc->predikat_badge }}" title="{{ $sc->predikat_label }}">
                                                                {{ $sc->predikat ?: strtoupper($sc->status_absensi) }}
                                                            </span>
                                                            @if($sc->catatan)
                                                                <small class="d-block text-muted mt-1 catatan-cell-text">{{ $sc->catatan }}</small>
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

<!-- Modal Detail Dokumentasi Pertemuan -->
<div class="modal fade" id="modalSessionDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detail_modal_title">Dokumentasi Pertemuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <small class="text-muted d-block">Tema Kegiatan:</small>
                    <strong id="detail_modal_tema"></strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Deskripsi Kegiatan:</small>
                    <p class="mb-0 small" id="detail_modal_deskripsi"></p>
                </div>
                <div id="detail_modal_foto_wrap" style="display: none;">
                    <small class="text-muted d-block mb-1">Foto Bukti Kegiatan:</small>
                    <img id="detail_modal_foto" src="" alt="Foto Bukti" class="img-fluid rounded border w-100">
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showSessionDetail(tanggalStr, tema, deskripsi, fotoUrl) {
        document.getElementById('detail_modal_title').textContent = 'Dokumentasi: ' + tanggalStr;
        document.getElementById('detail_modal_tema').textContent = tema;
        document.getElementById('detail_modal_deskripsi').textContent = deskripsi || '-';

        const fotoWrap = document.getElementById('detail_modal_foto_wrap');
        const fotoImg = document.getElementById('detail_modal_foto');
        if (fotoUrl) {
            fotoImg.src = fotoUrl;
            fotoWrap.style.display = 'block';
        } else {
            fotoImg.src = '';
            fotoWrap.style.display = 'none';
        }

        const modal = new bootstrap.Modal(document.getElementById('modalSessionDetail'));
        modal.show();
    }
</script>
@endpush
@endsection
