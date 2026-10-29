<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guru_mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_profile_id')->constrained('guru_profiles')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['guru_profile_id', 'mata_pelajaran_id']);
        });

        // Seed data historis dari mata_pelajaran_id yang sudah ada di guru_profiles
        $existingProfiles = DB::table('guru_profiles')
            ->whereNotNull('mata_pelajaran_id')
            ->select('id as guru_profile_id', 'mata_pelajaran_id')
            ->get();

        $now = now();
        $payload = [];
        foreach ($existingProfiles as $profile) {
            $payload[] = [
                'guru_profile_id' => $profile->guru_profile_id,
                'mata_pelajaran_id' => $profile->mata_pelajaran_id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($payload)) {
            DB::table('guru_mata_pelajaran')->insertOrIgnore($payload);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_mata_pelajaran');
    }
};

