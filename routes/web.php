<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [App\Http\Controllers\GuestController::class, 'welcome'])->name('welcome');

Auth::routes(['register' => false]);
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);

// School Profile Routes (Public Access)
Route::get('/data-sekolah', [App\Http\Controllers\SchoolController::class, 'index'])->name('school.profile');

// Main dashboard route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Role-based dashboard routes
Route::middleware(['auth'])->group(function () {
    // School Profile Management Routes (Admin and Kepala Sekolah)
    Route::middleware('role:admin,kepala_sekolah')->group(function () {
        Route::get('/data-sekolah/edit', [App\Http\Controllers\SchoolController::class, 'edit'])->name('school.edit');
        Route::put('/data-sekolah/update', [App\Http\Controllers\SchoolController::class, 'update'])->name('school.update');
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/guru', [App\Http\Controllers\Admin\AdminController::class, 'manageGuru'])->name('admin.guru.index');
        Route::get('/guru/create', [App\Http\Controllers\Admin\AdminController::class, 'createGuru'])->name('admin.guru.create');
        Route::get('/guru/search', [App\Http\Controllers\Admin\AdminController::class, 'searchGuru'])->name('admin.guru.search');
        Route::get('/guru/export', [App\Http\Controllers\Admin\AdminController::class, 'exportGuru'])->name('admin.guru.export');
        Route::post('/guru/store', [App\Http\Controllers\Admin\AdminController::class, 'storeGuru'])->name('admin.guru.store');
        Route::get('/guru/{id}', [App\Http\Controllers\Admin\AdminController::class, 'showGuru'])->name('admin.guru.show');
        Route::get('/guru/{id}/edit', [App\Http\Controllers\Admin\AdminController::class, 'editGuru'])->name('admin.guru.edit');
        Route::put('/guru/{id}', [App\Http\Controllers\Admin\AdminController::class, 'updateGuru'])->name('admin.guru.update');
        Route::delete('/guru/{id}', [App\Http\Controllers\Admin\AdminController::class, 'destroyGuru'])->name('admin.guru.destroy');

        // Siswa management routes
        Route::get('/siswa', [App\Http\Controllers\Admin\SiswaController::class, 'index'])->name('admin.siswa.index');
        Route::get('/siswa/create', [App\Http\Controllers\Admin\SiswaController::class, 'create'])->name('admin.siswa.create');
        Route::get('/siswa/search', [App\Http\Controllers\Admin\SiswaController::class, 'search'])->name('admin.siswa.search');
        Route::get('/siswa/export', [App\Http\Controllers\Admin\SiswaController::class, 'export'])->name('admin.siswa.export');
        Route::post('/siswa', [App\Http\Controllers\Admin\SiswaController::class, 'store'])->name('admin.siswa.store');
        Route::get('/siswa/{id}', [App\Http\Controllers\Admin\SiswaController::class, 'show'])->name('admin.siswa.show');
        Route::get('/siswa/{id}/edit', [App\Http\Controllers\Admin\SiswaController::class, 'edit'])->name('admin.siswa.edit');
        Route::put('/siswa/{id}', [App\Http\Controllers\Admin\SiswaController::class, 'update'])->name('admin.siswa.update');
        Route::delete('/siswa/{id}', [App\Http\Controllers\Admin\SiswaController::class, 'destroy'])->name('admin.siswa.destroy');

        // Jadwal Pelajaran routes
        Route::get('jadwal-mapel/get-by-kelas/{kelasId}', [App\Http\Controllers\Admin\JadwalPelajaranController::class, 'getByKelas'])->name('admin.jadwal.get-by-kelas');
        Route::resource('jadwal-mapel', App\Http\Controllers\Admin\JadwalPelajaranController::class)->names([
            'index' => 'admin.jadwal.index',
            'store' => 'admin.jadwal.store',
            'update' => 'admin.jadwal.update',
            'destroy' => 'admin.jadwal.destroy',
        ])->except(['create', 'edit', 'show']);

        // Mata Pelajaran routes
        Route::resource('mata-pelajaran', App\Http\Controllers\Admin\MataPelajaranController::class)->names([
            'index' => 'admin.mapel.index',
            'store' => 'admin.mapel.store',
            'update' => 'admin.mapel.update',
            'destroy' => 'admin.mapel.destroy',
        ])->except(['create', 'edit', 'show']);

        // Jam Pelajaran routes
        Route::resource('jam-pelajaran', App\Http\Controllers\Admin\JamPelajaranController::class)->names([
            'index' => 'admin.jam-pelajaran.index',
            'store' => 'admin.jam-pelajaran.store',
            'update' => 'admin.jam-pelajaran.update',
            'destroy' => 'admin.jam-pelajaran.destroy',
        ])->except(['create', 'edit', 'show']);

        // Fixed Schedule routes
        Route::resource('fixed-schedule', App\Http\Controllers\Admin\FixedScheduleController::class)->names([
            'index' => 'admin.fixed-schedule.index',
            'store' => 'admin.fixed-schedule.store',
            'destroy' => 'admin.fixed-schedule.destroy',
        ])->except(['create', 'edit', 'show', 'update']);
    });

    // Kepala Sekolah routes
    Route::prefix('kepala-sekolah')->middleware('role:kepala_sekolah')->group(function () {
        Route::get('/kepala-sekolah/dashboard', [App\Http\Controllers\KepalaSekolah\DashboardController::class, 'index'])->name('kepala-sekolah.dashboard');
    });

    // Guru routes
    Route::prefix('guru')->middleware('role:guru')->group(function () {
        Route::get('/guru/dashboard', [App\Http\Controllers\Guru\DashboardController::class, 'index'])->name('guru.dashboard');

        // Attendance routes
        Route::get('/absensi/siswa', [App\Http\Controllers\Guru\AbsensiController::class, 'absensiSiswa'])->name('guru.absensi.siswa');
        Route::get('/absensi/today', [App\Http\Controllers\Guru\AbsensiController::class, 'getTodayAttendance'])->name('guru.absensi.today');
        Route::post('/absensi/process', [App\Http\Controllers\Guru\AbsensiController::class, 'processAttendance'])->name('guru.absensi.process');
        Route::get('/absensi/qr/{nisn}', [App\Http\Controllers\Guru\AbsensiController::class, 'generateQRCode'])->name('guru.absensi.qr');
    });

    // Siswa routes
    Route::prefix('siswa')->middleware('role:siswa')->group(function () {
        Route::get('/siswa/dashboard', [App\Http\Controllers\Siswa\DashboardController::class, 'index'])->name('siswa.dashboard');
    });

    Route::prefix('/')->middleware('role:all')->group(function () {
        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/data', [App\Http\Controllers\ProfileController::class, 'getUserData'])->name('profile.data');
        Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/update-photo', [App\Http\Controllers\ProfileController::class, 'updateProfilePhoto'])->name('profile.update-photo');
        Route::post('/profile/update-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    });



    // // Profile routes
    // Route::middleware('auth')->group(function () {
    //     Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    //     Route::get('/profile/data', [App\Http\Controllers\ProfileController::class, 'getUserData'])->name('profile.data');
    //     Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    //     Route::post('/profile/update-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    // });
});
