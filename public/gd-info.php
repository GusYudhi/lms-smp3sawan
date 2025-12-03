<?php
// GD Library Information
echo "<h1>GD Library Information</h1>";

if (extension_loaded('gd')) {
    echo "<p>✅ GD Extension is loaded</p>";

    $gdInfo = gd_info();
    echo "<h2>GD Info:</h2>";
    echo "<pre>";
    print_r($gdInfo);
    echo "</pre>";

    echo "<h2>Image Format Support:</h2>";
    echo "<ul>";
    echo "<li>JPEG Support: " . (function_exists('imagecreatefromjpeg') ? '✅ YES' : '❌ NO') . "</li>";
    echo "<li>PNG Support: " . (function_exists('imagecreatefrompng') ? '✅ YES' : '❌ NO') . "</li>";
    echo "<li>GIF Support: " . (function_exists('imagecreatefromgif') ? '✅ YES' : '❌ NO') . "</li>";
    echo "<li>WebP Support (read): " . (function_exists('imagecreatefromwebp') ? '✅ YES' : '❌ NO') . "</li>";
    echo "<li>WebP Support (write): " . (function_exists('imagewebp') ? '✅ YES' : '❌ NO') . "</li>";
    echo "</ul>";

    echo "<h2>PHP Version:</h2>";
    echo "<p>" . phpversion() . "</p>";

    echo "<h2>Loaded Extensions:</h2>";
    echo "<pre>";
    print_r(get_loaded_extensions());
    echo "</pre>";
} else {
    echo "<p>❌ GD Extension is NOT loaded</p>";
}
