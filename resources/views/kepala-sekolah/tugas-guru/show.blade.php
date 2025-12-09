@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detail Tugas</h4>
                    <div>
                        <a href="{{ route('kepala-sekolah.tugas-guru.edit', $tugas->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('kepala-sekolah.tugas-guru.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h5>{{ $tugas->judul }}</h5>
                        <span class="badge bg-{{ $tugas->status_badge }}">{{ ucfirst($tugas->status) }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Deadline:</strong><br>
                        <i class="fas fa-calendar"></i> {{ $tugas->deadline->format('d F Y, H:i') }}
                        @if($tugas->isExpired())
                            <span class="badge bg-danger ms-2">Expired</span>
                        @else
                            <span class="badge bg-info ms-2">{{ $tugas->deadline->diffForHumans() }}</span>
                        @endif
                    </div>

                    @if($tugas->deskripsi)
                    <div class="mb-3">
                        <strong>Deskripsi:</strong>
                        <p class="mt-2">{{ $tugas->deskripsi }}</p>
                    </div>
                    @endif

                    @if($tugas->files->count() > 0)
                    <div class="mb-3">
                        <strong>Lampiran:</strong>
                        <ul class="list-group mt-2">
                            @foreach($tugas->files as $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file"></i> {{ $file->nama_file }}
                                    <br><small class="text-muted">{{ $file->formatted_size }}</small>
                                </div>
                                <a href="{{ asset('storage/' . $file->file_path) }}" class="btn btn-sm btn-primary" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="mb-3">
                        <strong>Dibuat oleh:</strong> {{ $tugas->creator->name }}<br>
                        <strong>Dibuat pada:</strong> {{ $tugas->created_at->format('d F Y, H:i') }}
                    </div>
                </div>
            </div>

            <!-- Submissions List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Pengumpulan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Guru</th>
                                    <th>Tanggal Submit</th>
                                    <th>Status</th>
                                    <th>Nilai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tugas->submissions as $submission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $submission->guru->name }}
                                        @if($submission->guru->guruProfile)
                                            <br><small class="text-muted">{{ $submission->guru->guruProfile->nip }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($submission->tanggal_submit)
                                            {{ $submission->tanggal_submit->format('d M Y, H:i') }}
                                            @if($submission->isLate())
                                                <br><span class="badge bg-danger">Terlambat</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Belum submit</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $submission->status_badge }}">
                                            {{ ucfirst($submission->status_pengumpulan) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($submission->nilai)
                                            <span class="badge bg-success">{{ $submission->nilai }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('kepala-sekolah.tugas-guru.show-submission', $submission->id) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">Belum ada guru yang mengumpulkan</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Statistik Pengumpulan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Guru:</span>
                            <strong>{{ $totalGuru }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Sudah Mengumpulkan:</span>
                            <strong>{{ $submittedCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between text-danger">
                            <span>Belum Mengumpulkan:</span>
                            <strong>{{ $notSubmittedCount }}</strong>
                        </div>
                    </div>

                    <div class="progress" style="height: 25px;">
                        @php
                            $percentage = $totalGuru > 0 ? ($submittedCount / $totalGuru) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: {{ $percentage }}%;"
                             aria-valuenow="{{ $percentage }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ number_format($percentage, 1) }}%
                        </div>
                    </div>
                </div>
            </div>

            @if($tugas->status == 'aktif')
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Perubahan Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('kepala-sekolah.tugas-guru.update', $tugas->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="judul" value="{{ $tugas->judul }}">
                        <input type="hidden" name="deskripsi" value="{{ $tugas->deskripsi }}">
                        <input type="hidden" name="deadline" value="{{ $tugas->deadline }}">

                        <select name="status" class="form-select mb-3">
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>

                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
