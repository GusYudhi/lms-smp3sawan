<div class="table-responsive shadow-sm rounded">
    <table class="table table-hover align-middle mb-0" id="teacherTable">
        <thead class="table-light">
            <tr>
                <th scope="col" class="text-center fw-semibold">#</th>
                <th scope="col" class="text-center fw-semibold">Foto</th>
                <th scope="col" class="fw-semibold">Nama Lengkap</th>
                <th scope="col" class="text-center fw-semibold">Jabatan</th>
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
                        <img src="{{ $teacher->getProfilePhotoUrl() }}"
                             alt="{{ $teacher->name }}"
                             class="rounded-circle"
                             style="width: 40px; height: 40px; object-fit: cover;">
                    </div>
                </td>
                <td>
                    <div class="text-start">
                        <div class="fw-semibold mb-1">{{ $teacher->name }}</div>
                        <div class="small">
                            <span class="text-muted fst-italic">NIP: {{ $teacher->guruProfile->nip ?? '-' }}</span>
                            @if($teacher->guruProfile->kode_guru)
                                <span class="badge bg-light text-dark border ms-1">{{ $teacher->guruProfile->kode_guru }}</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    @if($teacher->guruProfile && $teacher->guruProfile->jabatan_di_sekolah)
                        <span class="badge bg-info-subtle text-info border">
                            <i class="fas fa-user-tie me-1"></i>{{ $teacher->guruProfile->jabatan_di_sekolah }}
                        </span>
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($teacher->guruProfile && $teacher->guruProfile->mataPelajaran)
                        <span class="badge bg-primary-subtle text-primary border">{{ $teacher->guruProfile->mataPelajaran->nama_mapel }}</span>
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @php $status = $teacher->guruProfile->status_kepegawaian ?? '-'; @endphp
                    @if(strtolower($status) === 'pns')
                        <span class="badge bg-success-subtle text-success border">{{ $status }}</span>
                    @elseif(strtolower($status) === 'pppk')
                        <span class="badge bg-info-subtle text-info border">{{ $status }}</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border">{{ $status }}</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($teacher->guruProfile && $teacher->guruProfile->golongan)
                        <span class="badge bg-secondary">{{ $teacher->guruProfile->golongan }}</span>
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if(($teacher->guruProfile->jenis_kelamin ?? '') == 'L')
                        <span class="badge bg-primary-subtle text-primary border">
                            <i class="fas fa-mars me-1"></i>Laki-laki
                        </span>
                    @elseif(($teacher->guruProfile->jenis_kelamin ?? '') == 'P')
                        <span class="badge bg-danger-subtle text-danger border">
                            <i class="fas fa-venus me-1"></i>Perempuan
                        </span>
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($teacher->guruProfile && $teacher->guruProfile->kelas)
                        <span class="badge bg-warning-subtle text-warning border">
                            {{ $teacher->guruProfile->kelas->tingkat }} {{ $teacher->guruProfile->kelas->nama_kelas }}
                        </span>
                    @else
                        <span class="text-muted fst-italic">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($teacher->guruProfile && $teacher->guruProfile->nomor_telepon)
                        <a href="tel:{{ $teacher->guruProfile->nomor_telepon }}" class="text-decoration-none text-primary">
                            <i class="fas fa-phone me-1"></i>{{ $teacher->guruProfile->nomor_telepon }}
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
                        <h6 class="mb-2 fw-semibold">Tidak ada data pegawai</h6>
                        <p class="mb-0">Belum ada data pegawai yang ditemukan</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
