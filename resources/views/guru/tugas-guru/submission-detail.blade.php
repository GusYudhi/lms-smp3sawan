@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Detail Submission Saya</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>{{ $submission->tugasGuru->judul }}</h5>
                        <p class="text-muted">{{ $submission->tugasGuru->deskripsi }}</p>
                        <span class="badge bg-{{ $submission->status_badge }}">{{ ucfirst($submission->status_pengumpulan) }}</span>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Tanggal Submit:</strong><br>
                        @if($submission->tanggal_submit)
                            {{ $submission->tanggal_submit->format('d F Y, H:i') }}
                            @if($submission->isLate())
                                <span class="badge bg-danger ms-2">Terlambat</span>
                            @else
                                <span class="badge bg-success ms-2">Tepat Waktu</span>
                            @endif
                        @endif
                    </div>

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
                                <a href="{{ asset('storage/' . $file->file_path) }}" class="btn btn-sm btn-primary" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
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
                            <p class="mb-2"><strong>Nilai:</strong> <span class="badge bg-success fs-5">{{ $submission->nilai }}</span></p>
                        @endif
                        @if($submission->feedback)
                            <p class="mb-0"><strong>Feedback:</strong><br>{{ $submission->feedback }}</p>
                        @endif
                    </div>
                    @endif

                    <div class="d-flex justify-content-start">
                        <a href="{{ route('guru.tugas-guru.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
