<form action="{{ route('guru.tugas-guru.submit', $tugas->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="konten_tugas" class="form-label">Ketik Konten Tugas</label>
        <textarea class="form-control @error('konten_tugas') is-invalid @enderror"
                  id="konten_tugas" name="konten_tugas" rows="8"
                  placeholder="Tulis jawaban tugas Anda di sini...">{{ old('konten_tugas', $submission->konten_tugas ?? '') }}</textarea>
        @error('konten_tugas')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Anda dapat mengetik langsung konten tugas Anda</small>
    </div>

    <div class="mb-3">
        <label for="link_eksternal" class="form-label">Link Eksternal (Google Drive, dll)</label>
        <input type="url" class="form-control @error('link_eksternal') is-invalid @enderror"
               id="link_eksternal" name="link_eksternal"
               value="{{ old('link_eksternal', $submission->link_eksternal ?? '') }}"
               placeholder="https://drive.google.com/...">
        @error('link_eksternal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Atau tambahkan link eksternal (Google Drive, Dropbox, dll)</small>
    </div>

    <div class="mb-3">
        <label for="files" class="form-label">Upload File</label>
        <input type="file" class="form-control @error('files.*') is-invalid @enderror"
               id="files" name="files[]" multiple>
        @error('files.*')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Atau upload file (dapat lebih dari 1, maksimal 10MB per file)</small>
        <div id="file-list" class="mt-2"></div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Catatan:</strong> Anda harus mengisi salah satu dari: konten tugas, link eksternal, atau upload file.
    </div>

    <button type="submit" class="btn btn-success w-100">
        <i class="fas fa-paper-plane"></i> Kumpulkan Tugas
    </button>
</form>

@push('scripts')
<script>
document.getElementById('files').addEventListener('change', function(e) {
    const fileList = document.getElementById('file-list');
    fileList.innerHTML = '';

    if (this.files.length > 0) {
        const ul = document.createElement('ul');
        ul.className = 'list-group';

        Array.from(this.files).forEach((file, index) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';

            const fileSize = file.size < 1024 ? file.size + ' B' :
                            file.size < 1048576 ? (file.size / 1024).toFixed(2) + ' KB' :
                            (file.size / 1048576).toFixed(2) + ' MB';

            li.innerHTML = `
                <span><i class="fas fa-file"></i> ${file.name}</span>
                <span class="badge bg-secondary">${fileSize}</span>
            `;
            ul.appendChild(li);
        });

        fileList.appendChild(ul);
    }
});
</script>
@endpush
