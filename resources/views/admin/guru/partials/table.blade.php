<div class="table-wrapper">
    <table class="teacher-data-table" id="teacherTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Nama Lengkap</th>
                <th>NIP/NIK</th>
                <th>Mata Pelajaran</th>
                <th>Status Kepegawaian</th>
                <th>Golongan</th>
                <th>Jenis Kelamin</th>
                <th>Wali Kelas</th>
                <th>No. Telepon</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teachers as $index => $teacher)
            <tr class="teacher-row">
                <td>{{ $loop->iteration + ($teachers->currentPage() - 1) * $teachers->perPage() }}</td>
                <td>
                    <div class="teacher-photo">
                        <img src="{{ $teacher->profile_photo_path ? asset('storage/' . $teacher->profile_photo_path) : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiBmaWxsPSIjRTBFMEUwIi8+CjxwYXRoIGQ9Ik0yNSAxNUMxOS40NzcgMTUgMTUgMTkuNDc3IDE1IDI1QzE1IDMwLjUyMyAxOS40NzcgMzUgMjUgMzVDMzAuNTIzIDM1IDM1IDMwLjUyMyAzNSAyNUMzNSAxOS40NzcgMzAuNTIzIDE1IDI1IDE1WiIgZmlsbD0iIzk5OTk5OSIvPgo8L3N2Zz4K' }}"
                             alt="{{ $teacher->name }}">
                    </div>
                </td>
                <td>
                    <div class="teacher-name-info">
                        <strong>{{ $teacher->name }}</strong>
                        <small class="teacher-nip">NIP: {{ $teacher->nomor_induk }}</small>
                    </div>
                </td>
                <td>{{ $teacher->nomor_induk }}</td>
                <td>
                    <span class="subject-tag">{{ $teacher->mata_pelajaran }}</span>
                </td>
                <td>
                    <span class="status-badge status-{{ strtolower($teacher->status_kepegawaian) }}">
                        {{ $teacher->status_kepegawaian }}
                    </span>
                </td>
                <td>{{ $teacher->golongan ?: '-' }}</td>
                <td>
                    <span class="gender-badge gender-{{ strtolower($teacher->jenis_kelamin) }}">
                        {{ $teacher->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </td>
                <td>
                    @if($teacher->wali_kelas)
                        <span class="wali-kelas-badge">{{ $teacher->wali_kelas }}</span>
                    @else
                        <span class="no-wali">-</span>
                    @endif
                </td>
                <td>{{ $teacher->nomor_telepon ?: '-' }}</td>
                <td>
                    <span class="status-active">Aktif</span>
                </td>
                <td>
                    <div class="action-buttons-vertical">
                        <a href="{{ route('admin.guru.show', $teacher->id) }}" class="action-btn-vertical btn-view">
                            <i class="fas fa-eye"></i>
                            <span class="action-text">Lihat</span>
                        </a>
                        <a href="{{ route('admin.guru.edit', $teacher->id) }}" class="action-btn-vertical btn-edit">
                            <i class="fas fa-edit"></i>
                            <span class="action-text">Edit</span>
                        </a>
                        <button class="action-btn-vertical btn-delete" onclick="confirmDelete({{ $teacher->id }}, '{{ $teacher->name }}')">
                            <i class="fas fa-trash"></i>
                            <span class="action-text">Hapus</span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="text-center py-4">
                    <div style="padding: 40px; color: #666;">
                        <i class="fas fa-users" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p style="margin: 0; font-size: 16px;">Tidak ada data guru yang ditemukan</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
