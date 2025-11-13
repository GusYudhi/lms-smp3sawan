@extends('layouts.app')

@section('content')
<div class="siswa-dashboard">
    <h1>Dashboard Siswa</h1>

    <div class="student-overview">
        <div class="overview-card">
            <i class="fas fa-book-open"></i>
            <h3>Materi Terbaru</h3>
            <p>Akses materi pembelajaran</p>
        </div>

        <div class="overview-card">
            <i class="fas fa-clipboard-list"></i>
            <h3>Tugas Pending</h3>
            <p>0 tugas belum selesai</p>
        </div>

        <div class="overview-card">
            <i class="fas fa-chart-line"></i>
            <h3>Nilai Terbaru</h3>
            <p>Lihat progress nilai</p>
        </div>
    </div>

    <div class="upcoming-schedule">
        <h2>Jadwal Pelajaran Hari Ini</h2>
        <p>Persiapkan diri untuk pelajaran berikutnya</p>
    </div>
</div>
@endsection
