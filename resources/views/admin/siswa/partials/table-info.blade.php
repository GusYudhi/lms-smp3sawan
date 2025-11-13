Menampilkan {{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} siswa
@if(request()->hasAny(['search', 'jenis_kelamin', 'kelas']))
    <span class="filter-info">
        <i class="fas fa-filter text-primary"></i>
        (Hasil pencarian
        @if(request('search')) untuk "{{ request('search') }}" @endif
        @if(request('jenis_kelamin')) - {{ ucfirst(request('jenis_kelamin')) }} @endif
        @if(request('kelas')) - Kelas {{ request('kelas') }} @endif)
    </span>
@endif
