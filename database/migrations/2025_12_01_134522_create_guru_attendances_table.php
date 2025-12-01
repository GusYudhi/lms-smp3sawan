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
        Schema::create('guru_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->dateTime('waktu_absen');
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'alpha'])->default('hadir');
            $table->string('photo_path')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('distance_from_school', 8, 2)->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('tanggal');
            $table->index(['user_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_attendances');
    }
};
