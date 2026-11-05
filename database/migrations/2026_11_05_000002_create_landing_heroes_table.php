<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('landing_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable();
            $table->string('subjudul')->nullable();
            $table->string('gambar');
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        if (Schema::hasTable('school_profiles')) {
            Schema::table('school_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('school_profiles', 'hero_mode')) {
                    $table->enum('hero_mode', ['slideshow', 'static'])->default('slideshow')->after('npsn');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_heroes');

        if (Schema::hasTable('school_profiles') && Schema::hasColumn('school_profiles', 'hero_mode')) {
            Schema::table('school_profiles', function (Blueprint $table) {
                $table->dropColumn('hero_mode');
            });
        }
    }
};

