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
        Schema::create('school_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('SMPN 3 SAWAN');
            $table->text('visi');
            $table->json('misi'); // Store misi as JSON array
            $table->text('alamat');
            $table->string('telepon', 20);
            $table->string('email');
            $table->string('website')->nullable();
            $table->decimal('maps_latitude', 10, 7);
            $table->decimal('maps_longitude', 10, 7);
            $table->string('kepala_sekolah');
            $table->year('tahun_berdiri');
            $table->string('akreditasi', 2);
            $table->string('npsn', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_profiles');
    }
};
