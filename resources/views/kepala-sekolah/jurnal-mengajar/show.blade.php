@extends('layouts.app')

@section('title', 'Detail Jurnal Mengajar')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Detail Jurnal Mengajar</h4>
                </div>
                <div class="card-body">
                    <a href="{{ route('kepala-sekolah.jurnal-mengajar.index') }}" class="btn btn-secondary mb-3">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>

                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Tanggal</th>
                            <td>{{ $jurnal->tanggal->format('d F Y') }} ({{ $jurnal->hari }})</td>
                        </tr>
                        <tr>
                            <th>Guru</th>
                            <td>{{ $jurnal->guru->name }} - {{ $jurnal->guru->guruProfile->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>{{ $jurnal->kelas->tingkat }}{{ $jurnal->kelas->nama_kelas }}</td>
                        </tr>
                        <tr>
                            <th>Mata Pelajaran</th>
                            <td>{{ $jurnal->mataPelajaran->nama_mapel }}</td>
                        </tr>
                        <tr>
                            <th>Jam Pelajaran</th>
                            <td>Jam ke-{{ $jurnal->jam_ke_mulai }} s/d Jam ke-{{ $jurnal->jam_ke_selesai }}</td>
                        </tr>
                        <tr>
                            <th>Materi Pembelajaran</th>
                            <td>{{ $jurnal->materi_pembelajaran }}</td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $jurnal->keterangan ?? '-' }}</td>
                        </tr>
                        @if($jurnal->foto_bukti)
                        <tr>
                            <th>Foto Bukti</th>
                            <td>
                                <img src="{{ asset('storage/' . $jurnal->foto_bukti) }}" alt="Foto Bukti" style="max-width: 400px;" class="img-thumbnail">
                            </td>
                        </tr>
                        @endif
                    </table>

                    @if($jurnal->jurnalAttendances->isNotEmpty())
                    <h5 class="mt-4">Daftar Absensi Siswa</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jurnal->jurnalAttendances as $attendance)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $attendance->studentProfile->user->name ?? '-' }}</td>
                                    <td>{{ $attendance->studentProfile->nisn ?? '-' }}</td>
                                    <td>
                                        @if($attendance->status == 'hadir')
                                            <span class="badge bg-success">Hadir</span>
                                        @elseif($attendance->status == 'sakit')
                                            <span class="badge bg-warning">Sakit</span>
                                        @elseif($attendance->status == 'izin')
                                            <span class="badge bg-info">Izin</span>
                                        @elseif($attendance->status == 'alpha')
                                            <span class="badge bg-danger">Alpha</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->keterangan ?? '-' }}</td>
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
</div>
@endsection
