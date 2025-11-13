<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition()
    {
        $genders = ['Laki-laki', 'Perempuan'];
        $gender = $this->faker->randomElement($genders);

        return [
            'name' => $this->faker->name($gender === 'Laki-laki' ? 'male' : 'female'),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'nisn' => $this->faker->unique()->numerify('##########'),
            'nis' => $this->faker->unique()->numerify('S######'),
            'jenis_kelamin' => $gender,
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->date('Y-m-d', '-14 years'),
            'kelas' => $this->faker->randomElement(['7A','7B','7C','8A','8B','9A','9B']),
            'nomor_telepon_orangtua' => $this->faker->phoneNumber(),
            'nomor_telepon' => $this->faker->phoneNumber(),
            'profile_photo' => null,
            'is_active' => true,
        ];
    }
}
