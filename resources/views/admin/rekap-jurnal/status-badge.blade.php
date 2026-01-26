@props(['status'])

@php
    $color = 'secondary';
    $label = $status;
    
    // Status sesuai database: hadir, sakit, izin, alpa, terlambat
    switch(strtolower($status)) {
        case 'hadir': 
            $color = 'success'; 
            $label = 'H'; 
            break;
        case 'sakit': 
            $color = 'warning text-dark'; 
            $label = 'S'; 
            break;
        case 'izin': 
            $color = 'info'; 
            $label = 'I'; 
            break;
        case 'alpa': 
        case 'alpha': // jaga-jaga kalau ada typo lama
            $color = 'danger'; 
            $label = 'A'; 
            break;
        case 'terlambat': 
            $color = 'secondary'; 
            $label = 'T'; 
            break;
        default: 
            $color = 'light text-muted border'; 
            $label = '-'; 
            break;
    }
@endphp

<span class="badge bg-{{ $color }}" style="min-width: 25px;" title="{{ ucfirst($status) }}">{{ $label }}</span>
