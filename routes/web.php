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
Route::post('/saran', [App\Http\Controllers\GuestController::class, 'storeSaran'])->name('guest.saran.store');

Route::get('/guru-staff', [App\Http\Controllers\GuestController::class, 'guruStaff'])->name('guest.guru-staff');
Route::get('/berita', [App\Http\Controllers\GuestController::class, 'berita'])->name('guest.berita.index');
Route::get('/berita/{id}', [App\Http\Controllers\GuestController::class, 'showBerita'])->name('guest.berita.show');
Route::get('/prestasi', [App\Http\Controllers\GuestController::class, 'prestasi'])->name('guest.prestasi.index');
Route::get('/prestasi/{id}', [App\Http\Controllers\GuestController::class, 'showPrestasi'])->name('guest.prestasi.show');
Route::get('/galeri', [App\Http\Controllers\GuestController::class, 'galeri'])->name('guest.galeri');
Route::get('/kontak', [App\Http\Controllers\GuestController::class, 'kontak'])->name('guest.kontak');

Auth::routes(['register' => false]);
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);

// School Profile Routes (Public Access)
Route::get('/data-sekolah', [App\Http\Controllers\SchoolController::class, 'index'])->name('school.profile');

// Main dashboard route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Role-based dashboard routes
Route::middleware(['auth'])->group(function () {
    // HEIC Conversion endpoint (accessible by all authenticated users)
    Route::post('/convert-heic', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'convertHeic'])->name('convert-heic');

    // School Profile Management Routes (Admin and Kepala Sekolah)
    Route::middleware('role:admin,kepala_sekolah')->group(function () {
        Route::get('/data-sekolah/edit', [App\Http\Controllers\SchoolController::class, 'edit'])->name('school.edit');
        Route::put('/data-sekolah/update', [App\Http\Controllers\SchoolController::class, 'update'])->name('school.update');

    });

    // Shared routes for multiple roles
    Route::middleware('role:admin,guru,kepala_sekolah')->group(function () {
        Route::get('/rekap-jurnal', [App\Http\Controllers\Admin\RekapJurnalController::class, 'index'])->name('rekap-jurnal.index');
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/', function() {
            return redirect()->route('admin.dashboard');
        });
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/guru', [App\Http\Controllers\Admin\AdminController::class, 'manageGuru'])->name('admin.guru.index');
        Route::get('/guru/create', [App\Http\Controllers\Admin\AdminController::class, 'createGuru'])->name('admin.guru.create');
        Route::get('/guru/search', [App\Http\Controllers\Admin\AdminController::class, 'searchGuru'])->name('admin.guru.search');
        Route::get('/guru/export', [App\Http\Controllers\Admin\AdminController::class, 'exportGuru'])->name('admin.guru.export');

        // API for autocomplete
        Route::get('/api/mata-pelajaran', [App\Http\Controllers\Admin\AdminController::class, 'getMataPelajaran'])->name('admin.api.mata-pelajaran');
        Route::get('/api/kelas', [App\Http\Controllers\Admin\AdminController::class, 'getKelas'])->name('admin.api.kelas');
        Route::get('/guru/template', [App\Http\Controllers\Admin\AdminController::class, 'downloadTemplateGuru'])->name('admin.guru.template');
        Route::post('/guru/import', [App\Http\Controllers\Admin\AdminController::class, 'importGuru'])->name('admin.guru.import');
        Route::post('/guru/store', [App\Http\Controllers\Admin\AdminController::class, 'storeGuru'])->name('admin.guru.store');
        Route::get('/guru/{id}', [App\Http\Controllers\Admin\AdminController::class, 'showGuru'])->name('admin.guru.show');
        Route::get('/guru/{id}/edit', [App\Http\Controllers\Admin\AdminController::class, 'editGuru'])->name('admin.guru.edit');
        Route::put('/guru/{id}', [App\Http\Controllers\Admin\AdminController::class, 'updateGuru'])->name('admin.guru.update');
        Route::delete('/guru/{id}', [App\Http\Controllers\Admin\AdminController::class, 'destroyGuru'])->name('admin.guru.destroy');

        // Tahun Pelajaran & Semester routes
        Route::get('/tahun-pelajaran', [App\Http\Controllers\Admin\TahunPelajaranController::class, 'index'])->name('admin.tahun-pelajaran.index');
        Route::get('/tahun-pelajaran/create', [App\Http\Controllers\Admin\TahunPelajaranController::class, 'create'])->name('admin.tahun-pelajaran.create');
        Route::post('/tahun-pelajaran', [App\Http\Controllers\Admin\TahunPelajaranController::class, 'store'])->name('admin.tahun-pelajaran.store');
        Route::get('/tahun-pelajaran/{id}/dashboard', [App\Http\Controllers\Admin\TahunPelajaranController::class, 'dashboard'])->name('admin.tahun-pelajaran.dashboard');
        Route::get('/tahun-pelajaran/{id}/edit', [App\Http\Controllers\Admin\TahunPelajaranController::class, 'edit'])->name('admin.tahun-pelajaran.edit');
        Route::put('/tahun-pelajaran/{id}', [App\Http\Controllers\Admin\TahunPelajaranController::class, 'update'])->name('admin.tahun-pelajaran.update');
        Route::post('/tahun-pelajaran/{id}/set-active', [App\Http\Controllers\Admin\TahunPelajaranController::class, 'setActive'])->name('admin.tahun-pelajaran.set-active');
        Route::delete('/tahun-pelajaran/{id}', [App\Http\Controllers\Admin\TahunPelajaranController::class, 'destroy'])->name('admin.tahun-pelajaran.destroy');

        // Semester routes
        Route::get('/tahun-pelajaran/{tahunPelajaranId}/semester', [App\Http\Controllers\Admin\SemesterController::class, 'index'])->name('admin.semester.index');
        Route::group(['prefix' => '/semester'], function () {
            Route::get('/create', [App\Http\Controllers\Admin\SemesterController::class, 'create'])->name('admin.semester.create');
            Route::post('', [App\Http\Controllers\Admin\SemesterController::class, 'store'])->name('admin.semester.store');
            Route::get('/{semester_id}/dashboard', [App\Http\Controllers\Admin\SemesterController::class, 'dashboard'])->name('admin.semester.dashboard');
            Route::get('/{semester_id}/edit', [App\Http\Controllers\Admin\SemesterController::class, 'edit'])->name('admin.semester.edit');
            Route::put('/{semester_id}', [App\Http\Controllers\Admin\SemesterController::class, 'update'])->name('admin.semester.update');
            Route::post('/{semester_id}/set-active', [App\Http\Controllers\Admin\SemesterController::class, 'setActive'])->name('admin.semester.set-active');
            Route::post('/{semester_id}/import-from-semester-1', [App\Http\Controllers\Admin\SemesterController::class, 'importFromSemester1'])->name('admin.semester.import-from-semester-1');
            Route::delete('/{semester_id}', [App\Http\Controllers\Admin\SemesterController::class, 'destroy'])->name('admin.semester.destroy');
            // Jadwal Pelajaran routes
            Route::resource('/{semester_id}/jadwal-mapel', App\Http\Controllers\Admin\JadwalPelajaranController::class)->names([
                'index' => 'admin.jadwal.index',
                'store' => 'admin.jadwal.store',
                'update' => 'admin.jadwal.update',
                'destroy' => 'admin.jadwal.destroy',
            ])->except(['create', 'edit', 'show']);
            Route::get('/{semester_id}/jadwal-mapel/get-by-kelas/{kelasId}', [App\Http\Controllers\Admin\JadwalPelajaranController::class, 'getByKelas'])->name('admin.jadwal.get-by-kelas');
        });
        // Siswa management routes
        Route::get('/siswa', [App\Http\Controllers\Admin\SiswaController::class, 'index'])->name('admin.siswa.index');
        Route::get('/siswa/create', [App\Http\Controllers\Admin\SiswaController::class, 'create'])->name('admin.siswa.create');
        Route::get('/siswa/search', [App\Http\Controllers\Admin\SiswaController::class, 'search'])->name('admin.siswa.search');
        Route::get('/siswa/export', [App\Http\Controllers\Admin\SiswaController::class, 'export'])->name('admin.siswa.export');
        Route::post('/siswa/download-bulk-idcard', [App\Http\Controllers\Admin\SiswaController::class, 'downloadBulkIdCardZip'])->name('admin.siswa.download_bulk_idcard');
        Route::post('/siswa/upload-batch-idcard', [App\Http\Controllers\Admin\SiswaController::class, 'uploadBatchIdCards'])->name('admin.siswa.upload_batch_idcard');
        Route::post('/siswa/finalize-batch-download', [App\Http\Controllers\Admin\SiswaController::class, 'finalizeBatchDownload'])->name('admin.siswa.finalize_batch_download');
        Route::get('/siswa/template', [App\Http\Controllers\Admin\SiswaController::class, 'downloadTemplate'])->name('admin.siswa.template');
        Route::post('/siswa/import', [App\Http\Controllers\Admin\SiswaController::class, 'import'])->name('admin.siswa.import');
        Route::post('/siswa', [App\Http\Controllers\Admin\SiswaController::class, 'store'])->name('admin.siswa.store');
        Route::get('/siswa/{id}', [App\Http\Controllers\Admin\SiswaController::class, 'show'])->name('admin.siswa.show');
        Route::get('/siswa/{id}/edit', [App\Http\Controllers\Admin\SiswaController::class, 'edit'])->name('admin.siswa.edit');
        Route::put('/siswa/{id}', [App\Http\Controllers\Admin\SiswaController::class, 'update'])->name('admin.siswa.update');
        Route::delete('/siswa/{id}', [App\Http\Controllers\Admin\SiswaController::class, 'destroy'])->name('admin.siswa.destroy');



        // Mata Pelajaran routes
        Route::resource('mata-pelajaran', App\Http\Controllers\Admin\MataPelajaranController::class)->names([
            'index' => 'admin.mapel.index',
            'store' => 'admin.mapel.store',
            'update' => 'admin.mapel.update',
            'destroy' => 'admin.mapel.destroy',
        ])->except(['create', 'edit', 'show']);

        // Data Kelas routes
        Route::resource('kelas', App\Http\Controllers\Admin\KelasController::class)->names([
            'index' => 'admin.kelas.index',
            'store' => 'admin.kelas.store',
            'update' => 'admin.kelas.update',
            'destroy' => 'admin.kelas.destroy',
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

        // Rekap Absensi routes
        Route::get('/absensi', function () { return view('admin.absensi.index'); })->name('admin.absensi.index');
        Route::get('/absensi/siswa', [App\Http\Controllers\Admin\AbsensiRekapController::class, 'indexSiswa'])->name('admin.absensi.siswa.index');
        Route::get('/absensi/siswa/{id}', [App\Http\Controllers\Admin\AbsensiRekapController::class, 'detailSiswa'])->name('admin.absensi.siswa.detail');
        Route::get('/absensi/siswa/{id}/monthly', [App\Http\Controllers\Admin\AbsensiRekapController::class, 'monthlySiswa'])->name('admin.absensi.siswa.monthly');
        Route::post('/absensi/siswa/export', [App\Http\Controllers\Admin\AbsensiRekapController::class, 'exportSiswa'])->name('admin.absensi.siswa.export');
        // Route absensi guru dihapus - hanya tersedia untuk kepala sekolah
        // Route::get('/absensi/guru', [App\Http\Controllers\Admin\AbsensiRekapController::class, 'indexGuru'])->name('admin.absensi.guru.index');
        // Route::get('/absensi/guru/{id}', [App\Http\Controllers\Admin\AbsensiRekapController::class, 'detailGuru'])->name('admin.absensi.guru.detail');
        // Route::get('/absensi/guru/{id}/monthly', [App\Http\Controllers\Admin\AbsensiRekapController::class, 'monthlyGuru'])->name('admin.absensi.guru.monthly');

        // Kegiatan Kokurikuler
        Route::resource('kegiatan-kokurikuler', App\Http\Controllers\Admin\KegiatanKokurikulerController::class)->names('admin.kegiatan-kokurikuler');

        // Prestasi
        Route::resource('prestasi', App\Http\Controllers\Admin\PrestasiController::class)->names('admin.prestasi');

        // Berita
        Route::resource('berita', App\Http\Controllers\Admin\BeritaController::class)->names('admin.berita');

        // Saran
        Route::get('/saran', [App\Http\Controllers\Admin\SaranController::class, 'index'])->name('admin.saran.index');
        Route::put('/saran/{id}/status', [App\Http\Controllers\Admin\SaranController::class, 'updateStatus'])->name('admin.saran.update-status');
        Route::delete('/saran/{id}', [App\Http\Controllers\Admin\SaranController::class, 'destroy'])->name('admin.saran.destroy');

        // Galeri
        Route::resource('galeri', App\Http\Controllers\Admin\GaleriController::class)->names('admin.galeri');
    });

    // Kepala Sekolah routes
    Route::prefix('kepala-sekolah')->middleware('role:kepala_sekolah')->group(function () {
        Route::get('/kepala-sekolah/dashboard', [App\Http\Controllers\KepalaSekolah\DashboardController::class, 'index'])->name('kepala-sekolah.dashboard');

        // Data Guru (Read Only)
        Route::get('/data/guru', [App\Http\Controllers\KepalaSekolah\DataViewController::class, 'indexGuru'])->name('kepala-sekolah.guru.index');
        Route::get('/data/guru/{id}', [App\Http\Controllers\KepalaSekolah\DataViewController::class, 'showGuru'])->name('kepala-sekolah.guru.show');

        // Data Siswa (Read Only)
        Route::get('/data/siswa', [App\Http\Controllers\KepalaSekolah\DataViewController::class, 'indexSiswa'])->name('kepala-sekolah.siswa.index');
        Route::get('/data/siswa/{id}', [App\Http\Controllers\KepalaSekolah\DataViewController::class, 'showSiswa'])->name('kepala-sekolah.siswa.show');

        // Data Tahun Pelajaran (Read Only)
        Route::get('/data/tahun-pelajaran', [App\Http\Controllers\KepalaSekolah\DataViewController::class, 'indexTahunPelajaran'])->name('kepala-sekolah.tahun-pelajaran.index');
        Route::get('/data/tahun-pelajaran/{id}', [App\Http\Controllers\KepalaSekolah\DataViewController::class, 'showTahunPelajaran'])->name('kepala-sekolah.tahun-pelajaran.show');

        // Data Semester (Read Only)
        Route::get('/data/tahun-pelajaran/{tahunPelajaranId}/semester', [App\Http\Controllers\KepalaSekolah\DataViewController::class, 'indexSemester'])->name('kepala-sekolah.semester.index');
        Route::get('/data/semester/{id}', [App\Http\Controllers\KepalaSekolah\DataViewController::class, 'showSemester'])->name('kepala-sekolah.semester.show');

        // Rekap Absensi routes (Read Only)
        Route::get('/absensi', function () { return view('kepala-sekolah.absensi.index'); })->name('kepala-sekolah.absensi.index');
        Route::get('/absensi/siswa', [App\Http\Controllers\KepalaSekolah\AbsensiRekapController::class, 'indexSiswa'])->name('kepala-sekolah.absensi.siswa.index');
        Route::get('/absensi/siswa/{id}', [App\Http\Controllers\KepalaSekolah\AbsensiRekapController::class, 'detailSiswa'])->name('kepala-sekolah.absensi.siswa.detail');
        Route::get('/absensi/siswa/{id}/monthly', [App\Http\Controllers\KepalaSekolah\AbsensiRekapController::class, 'monthlySiswa'])->name('kepala-sekolah.absensi.siswa.monthly');
        Route::post('/absensi/siswa/export', [App\Http\Controllers\KepalaSekolah\AbsensiRekapController::class, 'exportSiswa'])->name('kepala-sekolah.absensi.siswa.export');
        Route::get('/absensi/guru', [App\Http\Controllers\KepalaSekolah\AbsensiRekapController::class, 'indexGuru'])->name('kepala-sekolah.absensi.guru.index');
        Route::get('/absensi/guru/{id}', [App\Http\Controllers\KepalaSekolah\AbsensiRekapController::class, 'detailGuru'])->name('kepala-sekolah.absensi.guru.detail');
        Route::get('/absensi/guru/{id}/monthly', [App\Http\Controllers\KepalaSekolah\AbsensiRekapController::class, 'monthlyGuru'])->name('kepala-sekolah.absensi.guru.monthly');
        Route::post('/absensi/guru/export', [App\Http\Controllers\KepalaSekolah\AbsensiRekapController::class, 'exportGuru'])->name('kepala-sekolah.absensi.guru.export');

        // Jadwal Pelajaran (Read Only)
        Route::get('/jadwal-pelajaran', [App\Http\Controllers\KepalaSekolah\JadwalPelajaranController::class, 'index'])->name('kepala-sekolah.jadwal-pelajaran.index');
        Route::get('/jadwal-pelajaran/get-by-kelas/{kelasId}', [App\Http\Controllers\KepalaSekolah\JadwalPelajaranController::class, 'getByKelas'])->name('kepala-sekolah.jadwal-pelajaran.get-by-kelas');

        // Jurnal Mengajar (Read Only)
        Route::get('/jurnal-mengajar', [App\Http\Controllers\KepalaSekolah\JurnalMengajarController::class, 'index'])->name('kepala-sekolah.jurnal-mengajar.index');
        Route::get('/jurnal-mengajar/{id}', [App\Http\Controllers\KepalaSekolah\JurnalMengajarController::class, 'show'])->name('kepala-sekolah.jurnal-mengajar.show');

        // Agenda Guru (Read Only)
        Route::get('/agenda-guru', [App\Http\Controllers\KepalaSekolah\AgendaGuruController::class, 'index'])->name('kepala-sekolah.agenda-guru.index');
        Route::get('/agenda-guru/{id}', [App\Http\Controllers\KepalaSekolah\AgendaGuruController::class, 'show'])->name('kepala-sekolah.agenda-guru.show');

        // Agenda Kepala Sekolah (Own Agenda Management)
        Route::get('/agenda', [App\Http\Controllers\KepalaSekolah\AgendaKepsekController::class, 'index'])->name('kepala-sekolah.agenda');
        Route::post('/agenda', [App\Http\Controllers\KepalaSekolah\AgendaKepsekController::class, 'store'])->name('kepala-sekolah.agenda.store');
        Route::put('/agenda/{id}', [App\Http\Controllers\KepalaSekolah\AgendaKepsekController::class, 'update'])->name('kepala-sekolah.agenda.update');
        Route::delete('/agenda/{id}', [App\Http\Controllers\KepalaSekolah\AgendaKepsekController::class, 'destroy'])->name('kepala-sekolah.agenda.destroy');

        // Tugas Guru routes
        Route::get('/tugas-guru', [App\Http\Controllers\KepalaSekolah\TugasGuruController::class, 'index'])->name('kepala-sekolah.tugas-guru.index');
        Route::get('/tugas-guru/create', [App\Http\Controllers\KepalaSekolah\TugasGuruController::class, 'create'])->name('kepala-sekolah.tugas-guru.create');
        Route::post('/tugas-guru', [App\Http\Controllers\KepalaSekolah\TugasGuruController::class, 'store'])->name('kepala-sekolah.tugas-guru.store');
        Route::get('/tugas-guru/{id}', [App\Http\Controllers\KepalaSekolah\TugasGuruController::class, 'show'])->name('kepala-sekolah.tugas-guru.show');
        Route::get('/tugas-guru/{id}/edit', [App\Http\Controllers\KepalaSekolah\TugasGuruController::class, 'edit'])->name('kepala-sekolah.tugas-guru.edit');
        Route::put('/tugas-guru/{id}', [App\Http\Controllers\KepalaSekolah\TugasGuruController::class, 'update'])->name('kepala-sekolah.tugas-guru.update');
        Route::delete('/tugas-guru/{id}', [App\Http\Controllers\KepalaSekolah\TugasGuruController::class, 'destroy'])->name('kepala-sekolah.tugas-guru.destroy');
        Route::delete('/tugas-guru/file/{id}', [App\Http\Controllers\KepalaSekolah\TugasGuruController::class, 'deleteFile'])->name('kepala-sekolah.tugas-guru.delete-file');
        Route::get('/tugas-guru/submission/{id}', [App\Http\Controllers\KepalaSekolah\TugasGuruController::class, 'showSubmission'])->name('kepala-sekolah.tugas-guru.show-submission');
        Route::put('/tugas-guru/submission/{id}/feedback', [App\Http\Controllers\KepalaSekolah\TugasGuruController::class, 'updateFeedback'])->name('kepala-sekolah.tugas-guru.update-feedback');

        // Kegiatan Kokurikuler
        Route::resource('kegiatan-kokurikuler', App\Http\Controllers\KepalaSekolah\KegiatanKokurikulerController::class)->names('kepala-sekolah.kegiatan-kokurikuler');

        // Prestasi
        Route::resource('prestasi', App\Http\Controllers\KepalaSekolah\PrestasiController::class)->names('kepala-sekolah.prestasi');

        // Berita
        Route::resource('berita', App\Http\Controllers\KepalaSekolah\BeritaController::class)->names('kepala-sekolah.berita');

        // Saran
        Route::get('/saran', [App\Http\Controllers\KepalaSekolah\SaranController::class, 'index'])->name('kepala-sekolah.saran.index');
        Route::put('/saran/{id}/status', [App\Http\Controllers\KepalaSekolah\SaranController::class, 'updateStatus'])->name('kepala-sekolah.saran.update-status');
        Route::delete('/saran/{id}', [App\Http\Controllers\KepalaSekolah\SaranController::class, 'destroy'])->name('kepala-sekolah.saran.destroy');

        // Galeri
        Route::resource('galeri', App\Http\Controllers\KepalaSekolah\GaleriController::class)->names('kepala-sekolah.galeri');
    });

    // Guru routes
    Route::prefix('guru')->middleware('role:guru')->group(function () {
        Route::get('/guru/dashboard', [App\Http\Controllers\Guru\DashboardController::class, 'index'])->name('guru.dashboard');

        // Attendance routes
        Route::get('/absensi/siswa', [App\Http\Controllers\Guru\AbsensiController::class, 'absensiSiswa'])->name('guru.absensi.siswa');
        Route::get('/absensi/today', [App\Http\Controllers\Guru\AbsensiController::class, 'getTodayAttendance'])->name('guru.absensi.today');
        Route::post('/absensi/process', [App\Http\Controllers\Guru\AbsensiController::class, 'processAttendance'])->name('guru.absensi.process');
        Route::get('/absensi/qr/{nisn}', [App\Http\Controllers\Guru\AbsensiController::class, 'generateQRCode'])->name('guru.absensi.qr');

        // Teacher Self-Attendance routes
        Route::get('/absensi-guru', [App\Http\Controllers\Guru\AbsensiGuruController::class, 'index'])->name('guru.absensi-guru');
        Route::post('/absensi-guru/store', [App\Http\Controllers\Guru\AbsensiGuruController::class, 'store'])->name('guru.absensi-guru.store');
        Route::post('/absensi-guru/store-non-hadir', [App\Http\Controllers\Guru\AbsensiGuruController::class, 'storeNonHadir'])->name('guru.absensi-guru.store-non-hadir');
        Route::get('/absensi-guru/weekly', [App\Http\Controllers\Guru\AbsensiGuruController::class, 'weekly'])->name('guru.absensi-guru.weekly');
        Route::get('/absensi-guru/monthly', [App\Http\Controllers\Guru\AbsensiGuruController::class, 'monthly'])->name('guru.absensi-guru.monthly');
        Route::get('/absensi-guru/school-location', [App\Http\Controllers\Guru\AbsensiGuruController::class, 'getSchoolLocation'])->name('guru.absensi-guru.school-location');

        // Student Attendance Recap routes for Guru
        Route::get('/rekap-absensi-siswa', [App\Http\Controllers\Guru\AbsensiRekapController::class, 'indexSiswa'])->name('guru.rekap-absensi.siswa');
        Route::get('/rekap-absensi-siswa/{id}', [App\Http\Controllers\Guru\AbsensiRekapController::class, 'detailSiswa'])->name('guru.rekap-absensi.siswa.detail');
        Route::get('/rekap-absensi-siswa/{id}/monthly', [App\Http\Controllers\Guru\AbsensiRekapController::class, 'monthlySiswa'])->name('guru.rekap-absensi.siswa.monthly');

        // Teaching Schedule routes
        Route::get('/jadwal-mengajar', [App\Http\Controllers\Guru\JadwalMengajarController::class, 'index'])->name('guru.jadwal-mengajar');
        Route::get('/jadwal-mengajar/today', [App\Http\Controllers\Guru\JadwalMengajarController::class, 'today'])->name('guru.jadwal-mengajar.today');
        Route::get('/jadwal-mengajar/get-by-kelas/{kelasId}', [App\Http\Controllers\Guru\JadwalMengajarController::class, 'getByKelas'])->name('guru.jadwal-mengajar.get-by-kelas');

        // Jurnal Mengajar routes
        Route::get('/jurnal-mengajar', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'index'])->name('guru.jurnal-mengajar.index');
        Route::get('/jurnal-mengajar/create', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'create'])->name('guru.jurnal-mengajar.create');
        Route::get('/jurnal-mengajar/wizard', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'createWizard'])->name('guru.jurnal-mengajar.wizard');
        Route::post('/jurnal-mengajar', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'store'])->name('guru.jurnal-mengajar.store');
        Route::get('/jurnal-mengajar/{id}', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'show'])->name('guru.jurnal-mengajar.show');
        Route::get('/jurnal-mengajar/{id}/edit', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'edit'])->name('guru.jurnal-mengajar.edit');
        Route::put('/jurnal-mengajar/{id}', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'update'])->name('guru.jurnal-mengajar.update');
        Route::delete('/jurnal-mengajar/{id}', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'destroy'])->name('guru.jurnal-mengajar.destroy');
        Route::get('/jurnal-mengajar/{id}/absensi', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'showAbsensi'])->name('guru.jurnal-mengajar.absensi');
        Route::post('/jurnal-mengajar/{id}/update-absensi', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'updateAbsensi'])->name('guru.jurnal-mengajar.update-absensi');
        Route::post('/jurnal-mengajar/{id}/update-jurnal-absensi', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'updateJurnalAbsensi'])->name('guru.jurnal-mengajar.update-jurnal-absensi');
        Route::get('/jurnal-mengajar/print', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'print'])->name('guru.jurnal-mengajar.print');

        // Materi Pelajaran
        Route::resource('materi', App\Http\Controllers\Guru\MateriPelajaranController::class)->names('guru.materi')->except(['edit', 'update', 'show']);

        // Tugas Guru routes
        Route::get('/tugas-guru', [App\Http\Controllers\Guru\TugasGuruController::class, 'index'])->name('guru.tugas-guru.index');
        Route::get('/tugas-guru/{id}', [App\Http\Controllers\Guru\TugasGuruController::class, 'show'])->name('guru.tugas-guru.show');
        Route::post('/tugas-guru/{id}/submit', [App\Http\Controllers\Guru\TugasGuruController::class, 'submit'])->name('guru.tugas-guru.submit');
        Route::get('/tugas-guru/submission/{id}', [App\Http\Controllers\Guru\TugasGuruController::class, 'showSubmission'])->name('guru.tugas-guru.show-submission');
        Route::delete('/tugas-guru/file/{id}', [App\Http\Controllers\Guru\TugasGuruController::class, 'deleteFile'])->name('guru.tugas-guru.delete-file');
        Route::delete('/tugas-guru/submission/{id}', [App\Http\Controllers\Guru\TugasGuruController::class, 'deleteSubmission'])->name('guru.tugas-guru.delete-submission');

        // Agenda Guru routes
        Route::get('/agenda', [App\Http\Controllers\Guru\AgendaGuruController::class, 'index'])->name('guru.agenda');
        Route::post('/agenda', [App\Http\Controllers\Guru\AgendaGuruController::class, 'store'])->name('guru.agenda.store');
        Route::put('/agenda/{id}', [App\Http\Controllers\Guru\AgendaGuruController::class, 'update'])->name('guru.agenda.update');
        Route::delete('/agenda/{id}', [App\Http\Controllers\Guru\AgendaGuruController::class, 'destroy'])->name('guru.agenda.destroy');
    });

    // Siswa routes
    Route::prefix('siswa')->middleware('role:siswa')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Siswa\DashboardController::class, 'index'])->name('siswa.dashboard');

        // Absensi routes
        Route::get('/absensi', [App\Http\Controllers\Siswa\AbsensiSiswaController::class, 'index'])->name('siswa.absensi.index');
        Route::post('/absensi/store', [App\Http\Controllers\Siswa\AbsensiSiswaController::class, 'store'])->name('siswa.absensi.store');
        Route::get('/absensi/weekly', [App\Http\Controllers\Siswa\AbsensiSiswaController::class, 'weekly'])->name('siswa.absensi.weekly');
        Route::get('/absensi/monthly', [App\Http\Controllers\Siswa\AbsensiSiswaController::class, 'monthly'])->name('siswa.absensi.monthly');

        // Jadwal Pelajaran routes
        Route::get('/jadwal-pelajaran', [App\Http\Controllers\Siswa\JadwalPelajaranController::class, 'index'])->name('siswa.jadwal-pelajaran.index');

        // Materi Pelajaran
        Route::get('/materi', [App\Http\Controllers\Siswa\MateriPelajaranController::class, 'index'])->name('siswa.materi.index');
        Route::get('/materi/{id}', [App\Http\Controllers\Siswa\MateriPelajaranController::class, 'show'])->name('siswa.materi.show');

        // Absensi Mapel
        Route::get('/absensi-mapel', [App\Http\Controllers\Siswa\AbsensiMapelController::class, 'index'])->name('siswa.absensi-mapel.index');
    });

    Route::prefix('/')->middleware('role:all')->group(function () {
        Route::get('/kegiatan-kokurikuler', [App\Http\Controllers\KegiatanKokurikulerController::class, 'index'])->name('kegiatan-kokurikuler.index');
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
