<div class="table-responsive shadow-sm rounded">
    <table class="table table-hover align-middle mb-0" id="teacherTable">
        <thead class="table-light">
            <tr>
                <th scope="col" class="text-center fw-semibold">#</th>
                <th scope="col" class="text-center fw-semibold">Foto</th>
                <th scope="col" class="fw-semibold">Nama Lengkap</th>
                <th scope="col" class="text-center fw-semibold">NIP/NIK</th>
                <th scope="col" class="text-center fw-semibold">Mata Pelajaran</th>
                <th scope="col" class="text-center fw-semibold">Status</th>
                <th scope="col" class="text-center fw-semibold">Golongan</th>
                <th scope="col" class="text-center fw-semibold">Gender</th>
                <th scope="col" class="text-center fw-semibold">Wali Kelas</th>
                <th scope="col" class="text-center fw-semibold">Telepon</th>
                <th scope="col" class="text-center fw-semibold">Status</th>
                <th scope="col" class="text-center fw-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teachers as $index => $teacher)
            <tr>
                <td class="text-center fw-bold">{{ $loop->iteration + ($teachers->currentPage() - 1) * $teachers->perPage() }}</td>
                <td class="text-center">
                    <div class="teacher-photo mx-auto rounded-circle overflow-hidden bg-light d-flex align-items-center justify-content-center">
                        @if($teacher->profile_photo_path)
                            <img src="{{ asset('storage/' . $teacher->profile_photo_path) }}"
                                 alt="{{ $teacher->name }}"
                                 class="rounded-circle">
                        @else
                            <div class="default-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="text-start">
                        <div class="fw-semibold mb-1">{{ $teacher->name }}</div>
                        <small class="text-muted fst-italic">NIP: {{ $teacher->nomor_induk }}</small>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge bg-light text-dark border">{{ $teacher->nomor_induk }}</span>
                </td>
                <td class="text-center">
                    <span class="badge bg-primary-subtle text-primary border">{{ $teacher->mata_pelajaran }}</span>
                </td>
                <td class="text-center">
                    @if(strtolower($teacher->status_kepegawaian) === 'pns')
                        <span class="badge bg-success-subtle text-success border">{{ $teacher->status_kepegawaian }}</span>
                    @elseif(strtolower($teacher->status_kepegawaian) === 'pppk')
                        <span class="badge bg-info-subtle text-info border">{{ $teacher->status_kepegawaian }}</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border">{{ $teacher->status_kepegawaian }}</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($teacher->golongan)
                        <span class="badge bg-secondary">{{ $teacher->golongan }}</span>
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($teacher->jenis_kelamin == 'L')
                        <span class="badge bg-primary-subtle text-primary border">
                            <i class="fas fa-mars me-1"></i>Laki-laki
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border">
                            <i class="fas fa-venus me-1"></i>Perempuan
                        </span>
                    @endif
                </td>
                <td class="text-center">
                    @if($teacher->wali_kelas)
                        <span class="badge bg-warning-subtle text-warning border">{{ $teacher->wali_kelas }}</span>
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($teacher->nomor_telepon)
                        <a href="tel:{{ $teacher->nomor_telepon }}" class="text-decoration-none text-primary">
                            <i class="fas fa-phone me-1"></i>{{ $teacher->nomor_telepon }}
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
                        <a href="{{ route('admin.guru.show', $teacher->id) }}"
                           class="btn btn-sm btn-outline-primary"
                           title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                            <span class="d-none d-lg-inline ms-1">Lihat</span>
                        </a>
                        <a href="{{ route('admin.guru.edit', $teacher->id) }}"
                           class="btn btn-sm btn-outline-warning"
                           title="Edit Data">
                            <i class="fas fa-edit"></i>
                            <span class="d-none d-lg-inline ms-1">Edit</span>
                        </a>
                        <button class="btn btn-sm btn-outline-danger"
                                onclick="confirmDelete({{ $teacher->id }}, '{{ addslashes($teacher->name) }}')"
                                title="Hapus Data">
                            <i class="fas fa-trash"></i>
                            <span class="d-none d-lg-inline ms-1">Hapus</span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-users fa-3x opacity-25 mb-3 d-block"></i>
                        <h6 class="mb-2 fw-semibold">Tidak ada data guru</h6>
                        <p class="mb-0">Belum ada data guru yang ditemukan</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
