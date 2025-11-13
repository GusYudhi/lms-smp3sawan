Menampilkan {{ $teachers->firstItem() ?? 0 }}-{{ $teachers->lastItem() ?? 0 }} dari {{ $teachers->total() }} guru
@if(request()->hasAny(['search', 'status', 'gender']))
    <span class="filter-info">
        <i class="fas fa-filter text-primary"></i>
        (Hasil pencarian
        @if(request('search')) untuk "{{ request('search') }}" @endif
        @if(request('status')) - {{ request('status') }} @endif
        @if(request('gender')) - {{ request('gender') === 'L' ? 'Laki-laki' : 'Perempuan' }} @endif)
    </span>
@endif
