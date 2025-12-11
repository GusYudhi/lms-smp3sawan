@extends('layouts.app')

@section('title', 'Detail Agenda Guru')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Detail Agenda Guru</h4>
                </div>
                <div class="card-body">
                    <a href="{{ route('kepala-sekolah.agenda-guru.index') }}" class="btn btn-secondary mb-3">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>

                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Tanggal</th>
                            <td>{{ $agenda->tanggal->format('d F Y') }} ({{ $agenda->tanggal->format('l') }})</td>
                        </tr>
                        <tr>
                            <th>Guru</th>
                            <td>{{ $agenda->user->name }} - {{ $agenda->user->guruProfile->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>{{ $agenda->kelas }}</td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td>
                                @if($agenda->jamMulai && $agenda->jamSelesai)
                                    {{ \Carbon\Carbon::parse($agenda->jamMulai->jam_mulai)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($agenda->jamSelesai->jam_selesai)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Materi</th>
                            <td>{{ $agenda->materi }}</td>
                        </tr>
                        <tr>
                            <th>Status Jurnal</th>
                            <td>
                                @if($agenda->status_jurnal == 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $agenda->keterangan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
