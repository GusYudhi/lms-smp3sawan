<?php
// Test file to check photo access
require_once '../vendor/autoload.php';

$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Storage;

echo "<h2>Photo Test</h2>";

// Get first student
$student = User::where('role', 'siswa')->with('studentProfile')->first();

if ($student) {
    echo "<h3>Student: " . $student->name . "</h3>";
    echo "<p>User ID: " . $student->id . "</p>";

    if ($student->studentProfile) {
        echo "<p>Student Profile ID: " . $student->studentProfile->id . "</p>";
        echo "<p>Foto Profil in DB: " . ($student->studentProfile->foto_profil ?? 'null') . "</p>";

        if ($student->studentProfile->foto_profil) {
            $filePath = 'public/profile_photos/' . $student->studentProfile->foto_profil;
            echo "<p>File Path Check: " . $filePath . "</p>";
            echo "<p>File Exists: " . (Storage::exists($filePath) ? 'YES' : 'NO') . "</p>";

            if (Storage::exists($filePath)) {
                $url = Storage::url('profile_photos/' . $student->studentProfile->foto_profil);
                echo "<p>Storage URL: " . $url . "</p>";
                echo "<p>Asset URL: " . asset('storage/profile_photos/' . $student->studentProfile->foto_profil) . "</p>";
                echo "<img src='" . asset('storage/profile_photos/' . $student->studentProfile->foto_profil) . "' style='width: 100px; height: 100px; object-fit: cover;'>";
            }
        }

        echo "<p>getProfilePhotoUrl(): " . $student->studentProfile->getProfilePhotoUrl() . "</p>";
        echo "<img src='" . $student->studentProfile->getProfilePhotoUrl() . "' style='width: 100px; height: 100px; object-fit: cover; border: 2px solid red;'>";
    }

    echo "<p>User getProfilePhotoUrl(): " . $student->getProfilePhotoUrl() . "</p>";
    echo "<img src='" . $student->getProfilePhotoUrl() . "' style='width: 100px; height: 100px; object-fit: cover; border: 2px solid blue;'>";
} else {
    echo "<p>No student found</p>";
}

// List all photos in storage
echo "<h3>Photos in Storage:</h3>";
$files = Storage::disk('public')->files('profile_photos');
foreach ($files as $file) {
    $filename = basename($file);
    echo "<p>" . $file . " - " . asset('storage/' . $file) . "</p>";
    echo "<img src='" . asset('storage/' . $file) . "' style='width: 50px; height: 50px; object-fit: cover; margin: 5px;'>";
}
?>
