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
        Schema::dropIfExists('prestasi_images');
        Schema::dropIfExists('berita_images');

        Schema::create('berita_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berita_id')->constrained('berita')->onDelete('cascade');
            $table->string('foto');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('prestasi_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestasi_id')->constrained('prestasi')->onDelete('cascade');
            $table->string('foto');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi_images');
        Schema::dropIfExists('berita_images');
    }
};
