<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::factory()->count(50)->create();
    }
}
