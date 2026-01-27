@extends('layouts.app')

@section('title', 'Detail Absensi Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2 text-success">
                                <i class="fas fa-user-graduate me-2"></i>Detail Absensi Siswa
                            </h1>
                            <p class="text-muted mb-0">Riwayat kehadiran siswa</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kepala-sekolah.absensi.siswa.index', ['filter' => request('filter'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                               class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profil Siswa -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <x-profile-photo
                            :src="$siswa->studentProfile && $siswa->studentProfile->foto_profil
                                ? asset('storage/profile_photos/' . $siswa->studentProfile->foto_profil)
                                : null"
                            :name="$siswa->name"
                            size="xl"
                            :clickable="true"
                        />
                    </div>
                    <h4 class="mb-1">{{ $siswa->name }}</h4>
                    <p class="text-muted mb-2">NISN: {{ $siswa->studentProfile->nisn ?? '-' }}</p>
                    @if($siswa->studentProfile && $siswa->studentProfile->kelas)
                    <span class="badge bg-primary fs-6">
                        {{ $siswa->studentProfile->kelas->tingkat }}{{ $siswa->studentProfile->kelas->nama_kelas }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Ringkasan Kehadiran
                    </h5>
                    <small class="text-muted" id="summary-month-label">
                        Bulan: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y') }}
                    </small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fas fa-check-circle text-success fs-2 mb-2"></i>
                                <h3 class="mb-1" id="summary-hadir">{{ $summary['hadir'] }}</h3>
                                <p class="text-muted mb-0 small">Hadir</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fas fa-procedures text-warning fs-2 mb-2"></i>
                                <h3 class="mb-1" id="summary-sakit">{{ $summary['sakit'] }}</h3>
                                <p class="text-muted mb-0 small">Sakit</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fas fa-file-alt text-info fs-2 mb-2"></i>
                                <h3 class="mb-1" id="summary-izin">{{ $summary['izin'] }}</h3>
                                <p class="text-muted mb-0 small">Izin</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fas fa-times-circle text-danger fs-2 mb-2"></i>
                                <h3 class="mb-1" id="summary-alpha">{{ $summary['alpha'] }}</h3>
                                <p class="text-muted mb-0 small">Alpha</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fas fa-clock text-secondary fs-2 mb-2"></i>
                                <h3 class="mb-1" id="summary-terlambat">{{ $summary['terlambat'] }}</h3>
                                <p class="text-muted mb-0 small">Terlambat</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-primary text-white">
                                <i class="fas fa-percentage fs-2 mb-2"></i>
                                <h3 class="mb-1" id="summary-persentase">{{ $persentaseHadir }}%</h3>
                                <p class="mb-0 small">Persentase Hadir</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Absensi -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Riwayat Absensi
                        </h5>
                        <button class="btn btn-sm btn-outline-primary" id="btn-current-month" title="Kembali ke bulan ini">
                            <i class="fas fa-redo"></i> Bulan Ini
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Month Navigation -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <button class="btn btn-outline-secondary" id="btn-prev-month">
                            <i class="fas fa-chevron-left"></i> Sebelumnya
                        </button>
                        <h5 class="mb-0" id="calendar-month-year">{{ date('F Y') }}</h5>
                        <button class="btn btn-outline-secondary" id="btn-next-month">
                            Selanjutnya <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Calendar Grid -->
                    <div id="calendar-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted d-block mb-2"><strong>Keterangan:</strong></small>
                        <div class="row g-2">
                            <div class="col-md-2 col-4">
                                <div class="d-flex align-items-center p-2 rounded" style="background-color: #28a745;">
                                    <small class="text-white fw-semibold">Hadir</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="d-flex align-items-center p-2 rounded" style="background-color: #ffc107;">
                                    <small class="text-dark fw-semibold">Izin</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="d-flex align-items-center p-2 rounded" style="background-color: #0dcaf0;">
                                    <small class="text-dark fw-semibold">Sakit</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="d-flex align-items-center p-2 rounded" style="background-color: #dc3545;">
                                    <small class="text-white fw-semibold">Alpha</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-4">
                                <div class="d-flex align-items-center p-2 rounded" style="background-color: #6c757d;">
                                    <small class="text-white fw-semibold">Terlambat</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    const userId = {{ $siswa->id }};

    document.addEventListener('DOMContentLoaded', function() {
        // Load calendar for current month
        loadMonthlyCalendar(currentYear, currentMonth);

        // Calendar navigation
        document.getElementById('btn-prev-month').addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            loadMonthlyCalendar(currentYear, currentMonth);
        });

        document.getElementById('btn-next-month').addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            loadMonthlyCalendar(currentYear, currentMonth);
        });

        document.getElementById('btn-current-month').addEventListener('click', function() {
            currentMonth = new Date().getMonth();
            currentYear = new Date().getFullYear();
            loadMonthlyCalendar(currentYear, currentMonth);
        });
    });

    function loadMonthlyCalendar(year, month) {
        // Update month-year display
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        document.getElementById('calendar-month-year').textContent = `${monthNames[month]} ${year}`;

        // Show loading
        document.getElementById('calendar-container').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        // Fetch monthly data
        fetch(`/kepala-sekolah/absensi/siswa/${userId}/monthly?year=${year}&month=${month + 1}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Monthly data:', data);
            if (data.success) {
                displayCalendar(year, month, data.data);
                
                // Update Summary Stats
                document.getElementById('summary-month-label').textContent = 'Bulan: ' + data.month_name;
                document.getElementById('summary-hadir').textContent = data.summary.hadir;
                document.getElementById('summary-sakit').textContent = data.summary.sakit;
                document.getElementById('summary-izin').textContent = data.summary.izin;
                document.getElementById('summary-alpha').textContent = data.summary.alpha;
                document.getElementById('summary-terlambat').textContent = data.summary.terlambat;
                document.getElementById('summary-persentase').textContent = data.persentase_hadir + '%';
            } else {
                throw new Error(data.message || 'Gagal memuat data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('calendar-container').innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat kalender
                </div>
            `;
        });
    }

    function displayCalendar(year, month, attendanceData) {
        const today = new Date();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDayOfWeek = firstDay.getDay(); // 0 = Sunday

        let html = '<div class="calendar-grid">';

        // Day headers
        const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        dayNames.forEach(day => {
            html += `<div class="calendar-header">${day}</div>`;
        });

        // Empty cells before first day
        for (let i = 0; i < startingDayOfWeek; i++) {
            html += '<div class="calendar-day empty"></div>';
        }

        // Days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const attendance = attendanceData[dateStr];

            let classes = 'calendar-day';
            let title = '';

            // Check if today
            if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
                classes += ' today';
            }

            // Add status class
            if (attendance) {
                classes += ` ${attendance.status}`;
                title = `${attendance.status_label}`;
                if (attendance.waktu) {
                    title += ` - ${attendance.waktu}`;
                }
                if (attendance.notes) {
                    title += `: ${attendance.notes}`;
                }
            }

            html += `<div class="${classes}" title="${title}">
                        <span class="day-number">${day}</span>
                     </div>`;
        }

        html += '</div>';
        document.getElementById('calendar-container').innerHTML = html;
    }
</script>
@endpush

<style>
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
    margin-bottom: 1rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.calendar-header {
    text-align: center;
    font-weight: 600;
    font-size: 0.7rem;
    padding: 0.4rem 0.2rem;
    background-color: #f8f9fa;
    color: #495057;
    border-radius: 0.25rem;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    border-radius: 0.25rem;
    background-color: #fff;
    border: 1px solid #e9ecef;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    min-height: 35px;
    max-height: 50px;
}

.calendar-day:hover:not(.empty) {
    transform: scale(1.05);
    box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    z-index: 1;
}

.calendar-day.empty {
    background-color: #f8f9fa;
    border: none;
    cursor: default;
}

.calendar-day.empty:hover {
    transform: none;
    box-shadow: none;
}

.calendar-day.today {
    border: 2px solid #0d6efd;
    font-weight: bold;
}

.calendar-day.hadir {
    background-color: #28a745;
    color: white;
    border-color: #28a745;
}

.calendar-day.izin {
    background-color: #0dcaf0;
    color: #000;
    border-color: #0dcaf0;
}

.calendar-day.sakit {
    background-color: #ffc107;
    color: #000;
    border-color: #ffc107;
}

.calendar-day.alpha {
    background-color: #dc3545;
    color: white;
    border-color: #dc3545;
}

.calendar-day.terlambat {
    background-color: #6c757d;
    color: white;
    border-color: #6c757d;
}

.calendar-day .day-number {
    position: relative;
    z-index: 1;
    font-weight: 600;
}

@media (max-width: 768px) {
    .calendar-grid {
        max-width: 100%;
    }

    .calendar-day {
        min-height: 32px;
        max-height: 40px;
        font-size: 0.7rem;
    }

    .calendar-header {
        font-size: 0.65rem;
        padding: 0.3rem 0.15rem;
    }
}
</style>
@endsection
