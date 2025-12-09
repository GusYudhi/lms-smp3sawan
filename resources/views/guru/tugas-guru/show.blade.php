@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Detail Tugas</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h5>{{ $tugas->judul }}</h5>
                        <small class="text-muted">Dibuat oleh: {{ $tugas->creator->name }}</small>
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
                        <strong>Deskripsi Tugas:</strong>
                        <p class="mt-2">{!! nl2br(e($tugas->deskripsi)) !!}</p>
                    </div>
                    @endif

                    @if($tugas->files->count() > 0)
                    <div class="mb-3">
                        <strong>Lampiran dari Kepala Sekolah:</strong>
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

                    <a href="{{ route('guru.tugas-guru.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Submission Form/Display -->
            @if($submission)
                <!-- Display existing submission -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Submission Anda</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $submission->status_badge }}">
                                {{ ucfirst($submission->status_pengumpulan) }}
                            </span>
                        </div>

                        @if($submission->tanggal_submit)
                        <div class="mb-3">
                            <strong>Tanggal Submit:</strong><br>
                            {{ $submission->tanggal_submit->format('d F Y, H:i') }}
                            @if($submission->isLate())
                                <span class="badge bg-danger ms-2">Terlambat</span>
                            @else
                                <span class="badge bg-success ms-2">Tepat Waktu</span>
                            @endif
                        </div>
                        @endif

                        @if($submission->konten_tugas)
                        <div class="mb-3">
                            <strong>Konten Tugas:</strong>
                            <div class="card mt-2">
                                <div class="card-body">
                                    {!! nl2br(e($submission->konten_tugas)) !!}
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($submission->link_eksternal)
                        <div class="mb-3">
                            <strong>Link Eksternal:</strong><br>
                            <a href="{{ $submission->link_eksternal }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                                <i class="fas fa-external-link-alt"></i> Buka Link
                            </a>
                        </div>
                        @endif

                        @if($submission->files->count() > 0)
                        <div class="mb-3">
                            <strong>File Lampiran:</strong>
                            <ul class="list-group mt-2">
                                @foreach($submission->files as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file"></i> {{ $file->nama_file }}
                                        <br><small class="text-muted">{{ $file->formatted_size }}</small>
                                    </div>
                                    <div>
                                        <a href="{{ asset('storage/' . $file->file_path) }}" class="btn btn-sm btn-primary" download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form action="{{ route('guru.tugas-guru.delete-file', $file->id) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus file ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if($submission->feedback || $submission->nilai)
                        <hr>
                        <div class="alert alert-info">
                            <h6><i class="fas fa-comment"></i> Feedback dari Kepala Sekolah</h6>
                            @if($submission->nilai)
                                <p class="mb-2"><strong>Nilai:</strong> <span class="badge bg-success fs-6">{{ $submission->nilai }}</span></p>
                            @endif
                            @if($submission->feedback)
                                <p class="mb-0"><strong>Feedback:</strong><br>{{ $submission->feedback }}</p>
                            @endif
                        </div>
                        @endif

                        <!-- Option to resubmit -->
                        <button type="button" class="btn btn-warning" data-bs-toggle="collapse" data-bs-target="#resubmitForm">
                            <i class="fas fa-edit"></i> Edit Submission
                        </button>
                        <form action="{{ route('guru.tugas-guru.delete-submission', $submission->id) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Yakin ingin menghapus submission ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Hapus Submission
                            </button>
                        </form>

                        <div class="collapse mt-3" id="resubmitForm">
                            <div class="card">
                                <div class="card-body">
                                    @include('guru.tugas-guru.partials.submission-form')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Submission Form for new submission -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Kumpulkan Tugas</h5>
                    </div>
                    <div class="card-body">
                        @if($tugas->isExpired())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Perhatian!</strong> Deadline sudah terlewat. Submission Anda akan ditandai sebagai terlambat.
                            </div>
                        @endif

                        @include('guru.tugas-guru.partials.submission-form')
                    </div>
                </div>
            @endif
        </div>

        <!-- Info Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Informasi</h5>
                </div>
                <div class="card-body">
                    <p><strong>Deadline:</strong><br>{{ $tugas->deadline->format('d F Y, H:i') }}</p>

                    @if($tugas->isExpired())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> Tugas sudah melewati deadline
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-clock"></i> Sisa waktu: {{ $tugas->deadline->diffForHumans(null, true) }}
                        </div>
                    @endif

                    @if($submission)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Anda sudah mengumpulkan tugas ini
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> Anda belum mengumpulkan tugas ini
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
