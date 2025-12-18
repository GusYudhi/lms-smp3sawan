<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class MarkAbsentStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:mark-absent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark students as Alpha if they have no attendance record for today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Get all active students
        $students = User::where('role', 'siswa')
            ->whereHas('studentProfile', function($q) {
                $q->where('is_active', true);
            })
            ->get();

        $count = 0;

        foreach ($students as $student) {
            // Check if student has attendance for today
            $hasAttendance = Attendance::where('user_id', $student->id)
                ->where('date', $today)
                ->exists();

            if (!$hasAttendance) {
                // Create Alpha record
                Attendance::create([
                    'user_id' => $student->id,
                    'date' => $today,
                    'time' => null, // No time for alpha
                    'status' => 'alpha',
                    'notes' => 'Otomatis oleh sistem (Tidak hadir sampai jam pelajaran terakhir)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }

        $this->info("Marked {$count} students as Alpha.");
    }
}
