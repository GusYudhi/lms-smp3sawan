@extends('layouts.app')

@section('title', 'Materi Pembelajaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Materi Pembelajaran</h1>
    </div>

    <div class="row">
        @php
            $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark'];
        @endphp
        @foreach($mapels as $index => $mapel)
        @php
            $color = $colors[$index % count($colors)];
        @endphp
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('siswa.materi.show', $mapel->id) }}" class="text-decoration-none">
                <div class="card bg-{{ $color }} text-white shadow h-100 py-2 hover-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="h5 mb-0 font-weight-bold">{{ $mapel->nama_mapel }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
