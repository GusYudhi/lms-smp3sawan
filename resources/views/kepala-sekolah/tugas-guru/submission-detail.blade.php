@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Detail Submission</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5>{{ $submission->tugasGuru->judul }}</h5>
                        <p class="text-muted mb-2">{{ $submission->tugasGuru->deskripsi }}</p>
                        <span class="badge bg-{{ $submission->status_badge }}">{{ ucfirst($submission->status_pengumpulan) }}</span>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Nama Guru:</strong><br>
                        {{ $submission->guru->name }}
                        @if($submission->guru->guruProfile)
                            <br><small class="text-muted">NIP: {{ $submission->guru->guruProfile->nip }}</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Tanggal Submit:</strong><br>
                        @if($submission->tanggal_submit)
                            {{ $submission->tanggal_submit->format('d F Y, H:i') }}
                            @if($submission->isLate())
                                <span class="badge bg-danger ms-2">Terlambat</span>
                            @else
                                <span class="badge bg-success ms-2">Tepat Waktu</span>
                            @endif
                        @else
                            <span class="text-muted">Belum submit</span>
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

                    <div class="d-flex justify-content-start">
                        <a href="{{ route('kepala-sekolah.tugas-guru.show', $submission->tugas_guru_id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Feedback & Penilaian</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('kepala-sekolah.tugas-guru.update-feedback', $submission->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nilai" class="form-label">Nilai (0-100)</label>
                            <input type="number" class="form-control" id="nilai" name="nilai"
                                   min="0" max="100" value="{{ old('nilai', $submission->nilai) }}">
                        </div>

                        <div class="mb-3">
                            <label for="feedback" class="form-label">Feedback</label>
                            <textarea class="form-control" id="feedback" name="feedback" rows="5">{{ old('feedback', $submission->feedback) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Simpan Feedback
                        </button>
                    </form>

                    @if($submission->nilai || $submission->feedback)
                    <hr>
                    <div class="mt-3">
                        <strong>Feedback Tersimpan:</strong>
                        @if($submission->nilai)
                            <div class="mt-2">
                                <span class="badge bg-success fs-5">Nilai: {{ $submission->nilai }}</span>
                            </div>
                        @endif
                        @if($submission->feedback)
                            <div class="mt-2">
                                <p>{{ $submission->feedback }}</p>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
