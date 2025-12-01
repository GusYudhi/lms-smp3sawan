@extends($semester ? 'layouts.semester' : 'layouts.app')

@section('title', 'Manajemen Jadwal Tetap')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Manajemen Jadwal Tetap</h1>
            @if($semester)
            <p class="text-muted mb-0">
                <small>{{ $semester->tahunPelajaran->nama }} - {{ $semester->nama }}</small>
            </p>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Jadwal Tetap</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.fixed-schedule.store') }}" method="POST">
                        @csrf
                        @if($semester)
                        <input type="hidden" name="semester_id" value="{{ $semester->id }}">
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Hari</label>
                            <select class="form-select" name="hari" required>
                                <option value="">Pilih Hari</option>
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                    <option value="{{ $hari }}">{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jam Ke-</label>
                            <select class="form-select" name="jam_ke" required>
                                <option value="">Pilih Jam</option>
                                @foreach($jamPelajarans as $jam)
                                    <option value="{{ $jam->jam_ke }}">Jam ke-{{ $jam->jam_ke }} ({{ \Carbon\Carbon::parse($jam->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jam->jam_selesai)->format('H:i') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" class="form-control" name="keterangan" placeholder="Contoh: Upacara Bendera" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal Tetap</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Jam Ke</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fixedSchedules as $fs)
                                    <tr>
                                        <td>{{ $fs->hari }}</td>
                                        <td>Jam ke-{{ $fs->jam_ke }}</td>
                                        <td>{{ $fs->keterangan }}</td>
                                        <td>
                                            <form action="{{ route('admin.fixed-schedule.destroy', $fs->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada jadwal tetap</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
