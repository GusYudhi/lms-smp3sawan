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

    'latitude' => env('SCHOOL_LATITUDE', -8.13308),
    'longitude' => env('SCHOOL_LONGITUDE', 115.15520),

    /*
    |--------------------------------------------------------------------------
    | Attendance Radius
    |--------------------------------------------------------------------------
    |
    | Radius maksimal dalam meter dari koordinat sekolah.
    | Guru hanya dapat melakukan absensi dalam radius ini.
    |
    */

    'attendance_radius' => env('SCHOOL_ATTENDANCE_RADIUS', 500),

    /*
    |--------------------------------------------------------------------------
    | Late Threshold
    |--------------------------------------------------------------------------
    |
    | Waktu batas untuk menentukan status terlambat (format: HH:MM)
    |
    */

    'late_threshold' => env('SCHOOL_LATE_THRESHOLD', '08:00'),
];
