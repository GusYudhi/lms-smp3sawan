@extends('layouts.app')

@section('content')
<div class="guru-dashboard">
    <h1>Dashboard Guru</h1>

    <div class="quick-actions">
        <div class="action-card">
            <i class="fas fa-upload"></i>
            <h3>Upload Materi</h3>
            <p>Bagikan materi pembelajaran</p>
        </div>

        <div class="action-card">
            <i class="fas fa-tasks"></i>
            <h3>Buat Tugas</h3>
            <p>Buat tugas untuk siswa</p>
        </div>

        <div class="action-card">
            <i class="fas fa-check-square"></i>
            <h3>Koreksi Nilai</h3>
            <p>Koreksi dan beri nilai tugas</p>
        </div>
    </div>

    <div class="schedule-overview">
        <h2>Jadwal Mengajar Hari Ini</h2>
        <p>Lihat jadwal mengajar dan kelola kelas</p>
    </div>
</div>
@endsection
