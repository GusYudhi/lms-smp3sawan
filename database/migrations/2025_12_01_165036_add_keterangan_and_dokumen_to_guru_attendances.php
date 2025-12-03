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
        // Check if columns exist before adding
        $hasKeterangan = Schema::hasColumn('guru_attendances', 'keterangan');
        $hasDokumenPath = Schema::hasColumn('guru_attendances', 'dokumen_path');
        $hasCatatan = Schema::hasColumn('guru_attendances', 'catatan');

        if (!$hasKeterangan || !$hasDokumenPath) {
            Schema::table('guru_attendances', function (Blueprint $table) use ($hasKeterangan, $hasDokumenPath) {
                // Add new keterangan column if not exists
                if (!$hasKeterangan) {
                    $table->text('keterangan')->nullable()->after('photo_path');
                }
                // Add dokumen_path column if not exists
                if (!$hasDokumenPath) {
                    $table->string('dokumen_path')->nullable()->after('photo_path');
                }
            });
        }

        // Copy data from catatan to keterangan if catatan exists and has data
        if ($hasCatatan && Schema::hasColumn('guru_attendances', 'keterangan')) {
            DB::statement('UPDATE guru_attendances SET keterangan = catatan WHERE catatan IS NOT NULL');
        }

        // Drop old catatan column if it exists
        if ($hasCatatan) {
            Schema::table('guru_attendances', function (Blueprint $table) {
                $table->dropColumn('catatan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back catatan column if it doesn't exist
        if (!Schema::hasColumn('guru_attendances', 'catatan')) {
            Schema::table('guru_attendances', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('photo_path');
            });
        }

        // Copy data back from keterangan to catatan if keterangan exists
        if (Schema::hasColumn('guru_attendances', 'keterangan')) {
            DB::statement('UPDATE guru_attendances SET catatan = keterangan WHERE keterangan IS NOT NULL');
        }

        // Drop keterangan and dokumen_path if they exist
        Schema::table('guru_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('guru_attendances', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
            if (Schema::hasColumn('guru_attendances', 'dokumen_path')) {
                $table->dropColumn('dokumen_path');
            }
        });
    }
};
