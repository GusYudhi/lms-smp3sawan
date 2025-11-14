<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Storage;

$student = User::where('role', 'siswa')->with('studentProfile')->first();

if ($student && $student->studentProfile) {
    echo "Student: " . $student->name . "\n";
    echo "Foto profil: " . ($student->studentProfile->foto_profil ?? 'null') . "\n";

    if ($student->studentProfile->foto_profil) {
        $filePath = 'public/profile_photos/' . $student->studentProfile->foto_profil;
        echo "File path: " . $filePath . "\n";
        echo "File exists: " . (Storage::exists($filePath) ? 'YES' : 'NO') . "\n";
    }

    echo "Photo URL: " . $student->studentProfile->getProfilePhotoUrl() . "\n";
    echo "User Photo URL: " . $student->getProfilePhotoUrl() . "\n";
} else {
    echo "No student or profile found\n";
}
