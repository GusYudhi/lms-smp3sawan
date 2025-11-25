<div class="table-responsive shadow-sm rounded">
    <table class="table table-hover align-middle mb-0" id="studentTable">
        <thead class="table-light">
            <tr>
                <th scope="col" class="text-center fw-semibold">#</th>
                <th scope="col" class="text-center fw-semibold">Foto</th>
                <th scope="col" class="fw-semibold">Nama Lengkap</th>
                <th scope="col" class="text-center fw-semibold">NIS</th>
                <th scope="col" class="text-center fw-semibold">NISN</th>
                <th scope="col" class="text-center fw-semibold">Gender</th>
                <th scope="col" class="text-center fw-semibold">Kelas</th>
                <th scope="col" class="text-center fw-semibold">Tanggal Lahir</th>
                <th scope="col" class="text-center fw-semibold">Email</th>
                <th scope="col" class="text-center fw-semibold">Status</th>
                <th scope="col" class="text-center fw-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
            <tr>
                <td class="text-center fw-bold">{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                <td class="text-center">
                    <div class="student-photo mx-auto rounded-circle overflow-hidden bg-light d-flex align-items-center justify-content-center">
                        @if($student->studentProfile && $student->studentProfile->foto_profil)
                            <img src="{{ asset('storage/' . $student->studentProfile->foto_profil) }}"
                                 alt="Foto {{ $student->name }}"
                                 class="rounded-circle">
                        @else
                            <div class="default-avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="text-start">
                        <div class="fw-semibold mb-1">{{ $student->name }}</div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge bg-light text-dark border">{{ $student->studentProfile->nis ?? '-' }}</span>
                </td>
                <td class="text-center">
                    @if($student->studentProfile && $student->studentProfile->nisn)
                        <span class="badge bg-light text-dark border">{{ $student->studentProfile->nisn }}</span>
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($student->studentProfile && $student->studentProfile->jenis_kelamin)
                        @if($student->studentProfile->jenis_kelamin === 'L')
                            <span class="badge bg-light text-primary border">
                                <i class="fas fa-mars me-1"></i>Laki-laki
                            </span>
                        @else
                            <span class="badge bg-light text-danger border">
                                <i class="fas fa-venus me-1"></i>Perempuan
                            </span>
                        @endif
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($student->studentProfile && $student->studentProfile->kelas)
                        <span class="badge bg-primary-subtle text-primary border">{{ $student->studentProfile->kelas }}</span>
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($student->studentProfile && $student->studentProfile->tanggal_lahir)
                        <span class="badge bg-light text-dark border">
                            {{ \Carbon\Carbon::parse($student->studentProfile->tanggal_lahir)->translatedFormat('d M Y') }}
                        </span>
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($student->email)
                        <a href="mailto:{{ $student->email }}" class="text-decoration-none text-primary">
                            <i class="fas fa-envelope me-1"></i>{{ $student->email }}
                        </a>
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    <span class="badge bg-success-subtle text-success border">
                        <i class="fas fa-check-circle me-1"></i>Aktif
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group-vertical gap-1" role="group">
                        <a href="{{ route('admin.siswa.show', $student->id) }}"
                           class="btn btn-sm btn-outline-primary"
                           title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                            <span class="d-none d-lg-inline ms-1">Lihat</span>
                        </a>
                        <a href="{{ route('admin.siswa.edit', $student->id) }}"
                           class="btn btn-sm btn-outline-warning"
                           title="Edit Data">
                            <i class="fas fa-edit"></i>
                            <span class="d-none d-lg-inline ms-1">Edit</span>
                        </a>
                        <button class="btn btn-sm btn-outline-danger"
                                onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->name) }}')"
                                title="Hapus Data">
                            <i class="fas fa-trash"></i>
                            <span class="d-none d-lg-inline ms-1">Hapus</span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-user-graduate fa-3x opacity-25 mb-3 d-block"></i>
                        <h6 class="mb-2 fw-semibold">Belum ada data siswa</h6>
                        <p class="mb-0">Belum ada data siswa yang tersedia</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
