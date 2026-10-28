<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Deduplikasi data kode_mapel jika sebelumnya ada duplikat antar semester
        $duplicates = DB::table('mata_pelajarans')
            ->select('kode_mapel', DB::raw('count(*) as count'))
            ->groupBy('kode_mapel')
            ->having('count', '>', 1)
            ->pluck('kode_mapel');

        foreach ($duplicates as $kode) {
            $records = DB::table('mata_pelajarans')
                ->where('kode_mapel', $kode)
                ->orderBy('id', 'asc')
                ->get();

            $primaryId = $records->first()->id;
            $duplicateIds = $records->slice(1)->pluck('id');

            // Re-point related tables
            if (Schema::hasTable('jadwal_pelajarans') && Schema::hasColumn('jadwal_pelajarans', 'mata_pelajaran_id')) {
                DB::table('jadwal_pelajarans')
                    ->whereIn('mata_pelajaran_id', $duplicateIds)
                    ->update(['mata_pelajaran_id' => $primaryId]);
            }

            if (Schema::hasTable('guru_profiles') && Schema::hasColumn('guru_profiles', 'mata_pelajaran_id')) {
                DB::table('guru_profiles')
                    ->whereIn('mata_pelajaran_id', $duplicateIds)
                    ->update(['mata_pelajaran_id' => $primaryId]);
            }

            if (Schema::hasTable('materi_pelajarans') && Schema::hasColumn('materi_pelajarans', 'mata_pelajaran_id')) {
                DB::table('materi_pelajarans')
                    ->whereIn('mata_pelajaran_id', $duplicateIds)
                    ->update(['mata_pelajaran_id' => $primaryId]);
            }

            if (Schema::hasTable('jurnal_mengajar') && Schema::hasColumn('jurnal_mengajar', 'mata_pelajaran_id')) {
                DB::table('jurnal_mengajar')
                    ->whereIn('mata_pelajaran_id', $duplicateIds)
                    ->update(['mata_pelajaran_id' => $primaryId]);
            }

            DB::table('mata_pelajarans')->whereIn('id', $duplicateIds)->delete();
        }

        // 2. Modifikasi tabel mata_pelajarans
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            if (Schema::hasColumn('mata_pelajarans', 'semester_id')) {
                $table->dropForeign(['semester_id']);
                $table->dropColumn('semester_id');
            }

            if (!Schema::hasColumn('mata_pelajarans', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_universal');
            }

            if (!Schema::hasColumn('mata_pelajarans', 'deleted_at')) {
                $table->softDeletes();
            }

            $table->unique('kode_mapel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->dropUnique(['kode_mapel']);

            if (Schema::hasColumn('mata_pelajarans', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (Schema::hasColumn('mata_pelajarans', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (!Schema::hasColumn('mata_pelajarans', 'semester_id')) {
                $table->unsignedBigInteger('semester_id')->nullable()->after('id');
                $table->foreign('semester_id')->references('id')->on('semester')->onDelete('set null');
            }
        });
    }
};
