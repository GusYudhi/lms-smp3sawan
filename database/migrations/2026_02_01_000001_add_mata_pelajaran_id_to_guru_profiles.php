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
        // 1. Add Foreign Key Column
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->foreignId('mata_pelajaran_id')->nullable()->after('golongan')->constrained('mata_pelajarans')->onDelete('set null');
        });

        // 2. Migrate Existing Data
        $profiles = DB::table('guru_profiles')->get();
        $mapels = DB::table('mata_pelajarans')->get()->keyBy('nama_mapel');

        foreach ($profiles as $profile) {
            if (empty($profile->mata_pelajaran)) continue;

            $subjects = [];
            $raw = $profile->mata_pelajaran;
            
            // Decode
            $decoded = json_decode($raw);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $subjects = $decoded;
            } else {
                $subjects = array_map('trim', explode(',', $raw));
            }

            // Take the FIRST subject found
            foreach ($subjects as $subjectName) {
                $subjectName = trim($subjectName, '"\'' );
                if (isset($mapels[$subjectName])) {
                    DB::table('guru_profiles')
                        ->where('id', $profile->id)
                        ->update(['mata_pelajaran_id' => $mapels[$subjectName]->id]);
                    break; // Stop after first match
                }
            }
        }

        // 3. Drop Old Column
        Schema::table('guru_profiles', function (Blueprint $table) {
             $table->dropColumn('mata_pelajaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Restore Column
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->json('mata_pelajaran')->nullable()->after('mata_pelajaran_id');
        });

        // 2. Restore Data
        $profiles = DB::table('guru_profiles')
            ->join('mata_pelajarans', 'guru_profiles.mata_pelajaran_id', '=', 'mata_pelajarans.id')
            ->select('guru_profiles.id', 'mata_pelajarans.nama_mapel')
            ->get();

        foreach ($profiles as $profile) {
            DB::table('guru_profiles')
                ->where('id', $profile->id)
                ->update(['mata_pelajaran' => json_encode([$profile->nama_mapel])]);
        }

        // 3. Drop Foreign Key Column
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->dropForeign(['mata_pelajaran_id']);
            $table->dropColumn('mata_pelajaran_id');
        });
    }
};