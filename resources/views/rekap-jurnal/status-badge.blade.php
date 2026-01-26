@props(['status', 'date' => null])

@php
    $badgeClass = 'bg-secondary';
    $text = $status;

    switch($status) {
        case 'H': $badgeClass = 'bg-success'; break;
        case 'I': $badgeClass = 'bg-info'; break;
        case 'S': $badgeClass = 'bg-warning text-dark'; break;
        case 'A': $badgeClass = 'bg-danger'; break;
        case 'T': $badgeClass = 'bg-secondary'; break;
    }
@endphp

<span class="badge {{ $badgeClass }} mb-1" title="{{ $date ? 'Tanggal: ' . $date : '' }}">
    {{ $text }} {{ $date ? '('.$date.')' : '' }}
</span>
