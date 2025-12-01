<?php

return [
    /*
    |--------------------------------------------------------------------------
    | School Location Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi koordinat GPS sekolah untuk validasi absensi guru.
    | Gunakan Google Maps untuk mendapatkan koordinat yang akurat.
    |
    */

    'latitude' => env('SCHOOL_LATITUDE', -8.6705),
    'longitude' => env('SCHOOL_LONGITUDE', 115.2126),

    /*
    |--------------------------------------------------------------------------
    | Attendance Radius
    |--------------------------------------------------------------------------
    |
    | Radius maksimal dalam meter dari koordinat sekolah.
    | Guru hanya dapat melakukan absensi dalam radius ini.
    |
    */

    'attendance_radius' => env('SCHOOL_ATTENDANCE_RADIUS', 100),

    /*
    |--------------------------------------------------------------------------
    | Late Threshold
    |--------------------------------------------------------------------------
    |
    | Waktu batas untuk menentukan status terlambat (format: HH:MM)
    |
    */

    'late_threshold' => env('SCHOOL_LATE_THRESHOLD', '07:30'),
];
