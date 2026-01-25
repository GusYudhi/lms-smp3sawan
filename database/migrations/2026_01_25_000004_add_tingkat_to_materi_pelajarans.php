<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('materi_pelajarans')) {
            Schema::table('materi_pelajarans', function (Blueprint $table) {
                $table->integer('tingkat')->nullable()->after('kelas_id'); // 7, 8, 9
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('materi_pelajarans')) {
            Schema::table('materi_pelajarans', function (Blueprint $table) {
                $table->dropColumn('tingkat');
            });
        }
    }
};
