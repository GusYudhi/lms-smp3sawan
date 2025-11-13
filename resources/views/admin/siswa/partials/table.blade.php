<table class="student-data-table" id="studentTable">
    <thead>
        <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Nama Lengkap</th>
            <th>NIS</th>
            <th>NISN</th>
            <th>Jenis Kelamin</th>
            <th>Kelas</th>
            <th>Telp. Orang Tua</th>
            <th>Tanggal Lahir</th>
            <th>No. Telepon</th>
            <th>Email</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($students as $index => $student)
        <tr class="student-row">
            <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
            <td>
                <div class="student-photo">
                    @if($student->profile_photo)
                        <img src="{{ asset('storage/' . $student->profile_photo) }}"
                             alt="Foto {{ $student->name }}"
                             class="student-photo-img">
                    @else
                        <div class="default-avatar">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    @endif
                </div>
            </td>
            <td>
                <div class="student-name-info">
                    <strong>{{ $student->name }}</strong>
                    <small class="student-nis">NIS: {{ $student->nis }}</small>
                </div>
            </td>
            <td>{{ $student->nis }}</td>
            <td>{{ $student->nisn ?? '-' }}</td>
            <td>
                <span class="gender-badge gender-{{ strtolower($student->jenis_kelamin) }}">
                    <i class="fas fa-{{ $student->jenis_kelamin === 'laki-laki' ? 'mars' : 'venus' }}"></i>
                    {{ ucfirst($student->jenis_kelamin) }}
                </span>
            </td>
            <td>
                @if($student->kelas)
                    <span class="class-badge">{{ $student->kelas }}</span>
                @else
                    <span class="no-class">-</span>
                @endif
            </td>
            <td>{{ $student->nomor_telepon_orangtua ?? '-' }}</td>
            <td>
                @if($student->tanggal_lahir)
                    {{ \Carbon\Carbon::parse($student->tanggal_lahir)->translatedFormat('d M Y') }}
                @else
                    -
                @endif
            </td>
            <td>{{ $student->nomor_telepon ?? '-' }}</td>
            <td>
                @if($student->email)
                    <a href="mailto:{{ $student->email }}" class="email-link">{{ $student->email }}</a>
                @else
                    -
                @endif
            </td>
            <td>
                <span class="status-active">Aktif</span>
            </td>
            <td>
                <div class="action-buttons-vertical">
                    <a href="{{ route('admin.siswa.show', $student->id) }}" class="action-btn-vertical btn-view">
                        <i class="fas fa-eye"></i>
                        <span class="btn-tooltip">Lihat</span>
                    </a>
                    <a href="{{ route('admin.siswa.edit', $student->id) }}" class="action-btn-vertical btn-edit">
                        <i class="fas fa-edit"></i>
                        <span class="btn-tooltip">Edit</span>
                    </a>
                    <button class="action-btn-vertical btn-delete" onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->name) }}')">
                        <i class="fas fa-trash"></i>
                        <span class="btn-tooltip">Hapus</span>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="13" class="text-center py-4">
                <div style="padding: 40px; color: #666;">
                    <i class="fas fa-users" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p style="margin: 0; font-size: 16px;">Belum ada data siswa</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
