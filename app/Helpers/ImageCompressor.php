<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageCompressor
{
    /**
     * Compress and convert image to WebP format
     * Falls back to original upload if compression fails
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param string $filename
     * @param int $quality
     * @param int $maxWidth
     * @return string Path to the stored file
     */
    public static function compressAndStore($file, $directory, $filename = null, $quality = 80, $maxWidth = 1200)
    {
        // Generate filename if not provided
        if (!$filename) {
            $filename = time() . '_' . uniqid();
        }

        // Remove extension from filename if exists
        $filename = pathinfo($filename, PATHINFO_FILENAME);

        // Check if WebP is supported, if not, skip compression immediately
        if (!function_exists('imagewebp')) {
            Log::info('⚠️ WebP compression not available, storing original image', [
                'reason' => 'imagewebp() function not available',
                'gd_version' => defined('GD_VERSION') ? GD_VERSION : 'Unknown',
                'supported_formats' => self::getSupportedFormats(),
                'recommendation' => 'Install PHP with WebP-enabled GD extension for image compression'
            ]);
            return self::storeOriginal($file, $directory, $filename);
        }

        try {
            // Try to compress with Intervention Image
            return self::compressWithIntervention($file, $directory, $filename, $quality, $maxWidth);
        } catch (\Exception $e) {
            // Log the error with GD info
            Log::warning('Image compression failed, falling back to direct upload', [
                'error' => $e->getMessage(),
                'file' => $filename,
                'gd_version' => defined('GD_VERSION') ? GD_VERSION : 'Unknown',
                'gd_loaded' => extension_loaded('gd') ? 'YES' : 'NO',
                'webp_support' => function_exists('imagewebp') ? 'YES' : 'NO',
                'jpeg_support' => function_exists('imagecreatefromjpeg') ? 'YES' : 'NO',
                'png_support' => function_exists('imagecreatefrompng') ? 'YES' : 'NO',
                'supported_formats' => self::getSupportedFormats()
            ]);

            // Fallback: Store original file without compression
            return self::storeOriginal($file, $directory, $filename);
        }
    }

    /**
     * Compress image using Intervention Image
     */
    private static function compressWithIntervention($file, $directory, $filename, $quality, $maxWidth)
    {
        // Check if GD functions are available
        if (!extension_loaded('gd')) {
            throw new \Exception('GD extension is not loaded');
        }

        // Check for WebP support - REQUIRED
        if (!function_exists('imagewebp')) {
            throw new \Exception('GD extension does not support WebP format. Please enable WebP in php.ini or use a different PHP build with WebP support.');
        }

        // Get file mime type
        $mimeType = $file->getMimeType();

        // Check if we can load this image type
        if (!self::canLoadImageType($mimeType)) {
            throw new \Exception("GD cannot load image type: {$mimeType}. Supported formats: " . self::getSupportedFormats());
        }

        // Add .webp extension
        $webpFilename = $filename . '.webp';

        // Get the full path
        $fullPath = storage_path('app/public/' . $directory);

        // Create directory if doesn't exist
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Use GD directly for better compatibility
        $sourceImage = self::loadImageWithGD($file->getRealPath(), $file->getMimeType());

        if (!$sourceImage) {
            throw new \Exception('Failed to load image with GD');
        }

        // Get original dimensions
        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        // Calculate new dimensions if resizing needed
        if ($originalWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) ($originalHeight * ($maxWidth / $originalWidth));
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }

        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);

        // Resize image
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Save as WebP
        $savePath = $fullPath . '/' . $webpFilename;
        $saveSuccess = imagewebp($newImage, $savePath, $quality);

        if (!$saveSuccess) {
            // Free memory before throwing
            imagedestroy($sourceImage);
            imagedestroy($newImage);
            throw new \Exception('Failed to save WebP image');
        }

        // Get file size info
        $originalSize = $file->getSize();
        $compressedSize = file_exists($savePath) ? filesize($savePath) : 0;
        $savings = $originalSize > 0 ? round((($originalSize - $compressedSize) / $originalSize) * 100, 2) : 0;

        // Verify the saved file is actually WebP
        $savedMimeType = mime_content_type($savePath);
        $isWebP = ($savedMimeType === 'image/webp');

        // Log successful compression
        Log::info('✅ Image compressed successfully', [
            'original_name' => $file->getClientOriginalName(),
            'original_format' => strtoupper($file->getClientOriginalExtension()),
            'original_mime' => $file->getMimeType(),
            'original_size' => number_format($originalSize / 1024, 2) . ' KB',
            'original_dimensions' => $originalWidth . 'x' . $originalHeight,
            'compressed_file' => $webpFilename,
            'compressed_format' => 'WEBP',
            'compressed_mime' => $savedMimeType,
            'is_webp_verified' => $isWebP ? '✓ YES' : '✗ NO',
            'compressed_size' => number_format($compressedSize / 1024, 2) . ' KB',
            'new_dimensions' => $newWidth . 'x' . $newHeight,
            'quality' => $quality . '%',
            'savings' => $savings . '%',
            'full_path' => $savePath,
            'relative_path' => $directory . '/' . $webpFilename
        ]);

        // Alert if not WebP (should not happen but safety check)
        if (!$isWebP) {
            Log::warning('⚠️ Saved file is not WebP format!', [
                'expected' => 'image/webp',
                'actual' => $savedMimeType,
                'file' => $webpFilename
            ]);
        }

        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);

        // Return the relative path
        return $directory . '/' . $webpFilename;
    }

    /**
     * Check if GD can load this image type
     */
    private static function canLoadImageType($mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return function_exists('imagecreatefromjpeg');
            case 'image/png':
                return function_exists('imagecreatefrompng');
            case 'image/gif':
                return function_exists('imagecreatefromgif');
            case 'image/webp':
                return function_exists('imagecreatefromwebp');
            default:
                return false;
        }
    }

    /**
     * Get list of supported image formats
     */
    private static function getSupportedFormats()
    {
        $formats = [];
        if (function_exists('imagecreatefromjpeg')) $formats[] = 'JPEG';
        if (function_exists('imagecreatefrompng')) $formats[] = 'PNG';
        if (function_exists('imagecreatefromgif')) $formats[] = 'GIF';
        if (function_exists('imagecreatefromwebp')) $formats[] = 'WebP';

        return empty($formats) ? 'None' : implode(', ', $formats);
    }

    /**
     * Load image using GD based on mime type
     */
    private static function loadImageWithGD($path, $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                return false;
        }
    }

    /**
     * Store original file without compression (fallback)
     */
    private static function storeOriginal($file, $directory, $filename)
    {
        $extension = $file->getClientOriginalExtension();
        $fullFilename = $filename . '.' . $extension;

        $file->storeAs($directory, $fullFilename, 'public');

        // Log fallback usage
        Log::warning('⚠️ Image stored without compression (fallback)', [
            'original_name' => $file->getClientOriginalName(),
            'format' => strtoupper($extension),
            'size' => number_format($file->getSize() / 1024, 2) . ' KB',
            'stored_as' => $fullFilename,
            'path' => $directory . '/' . $fullFilename
        ]);

        return $directory . '/' . $fullFilename;
    }

    /**
     * Compress existing image file to WebP
     *
     * @param string $existingPath Path relative to storage/app/public
     * @param int $quality
     * @param int $maxWidth
     * @return string|null Path to the compressed file or null if failed
     */
    public static function compressExisting($existingPath, $quality = 80, $maxWidth = 1200)
    {
        $fullPath = storage_path('app/public/' . $existingPath);

        if (!file_exists($fullPath)) {
            return null;
        }

        try {
            // Get mime type
            $mimeType = mime_content_type($fullPath);

            // Load image with GD
            $sourceImage = self::loadImageWithGD($fullPath, $mimeType);

            if (!$sourceImage) {
                return null;
            }

            // Get original dimensions
            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);

            // Calculate new dimensions if resizing needed
            if ($originalWidth > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) ($originalHeight * ($maxWidth / $originalWidth));
            } else {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
            }

            // Create new image
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);

            // Resize
            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            // Generate new filename with .webp extension
            $pathInfo = pathinfo($existingPath);
            $newFilename = $pathInfo['filename'] . '.webp';
            $newPath = $pathInfo['dirname'] . '/' . $newFilename;
            $fullNewPath = storage_path('app/public/' . $newPath);

            // Get original file size
            $originalSize = filesize($fullPath);

            // Save as WebP
            $saveSuccess = imagewebp($newImage, $fullNewPath, $quality);

            if (!$saveSuccess) {
                imagedestroy($sourceImage);
                imagedestroy($newImage);
                throw new \Exception('Failed to save WebP image');
            }

            // Get compressed file size
            $compressedSize = filesize($fullNewPath);
            $savings = $originalSize > 0 ? round((($originalSize - $compressedSize) / $originalSize) * 100, 2) : 0;

            // Verify the saved file is actually WebP
            $savedMimeType = mime_content_type($fullNewPath);
            $isWebP = ($savedMimeType === 'image/webp');

            // Log successful compression
            Log::info('✅ Existing image compressed successfully', [
                'original_file' => basename($existingPath),
                'original_format' => strtoupper($pathInfo['extension'] ?? 'unknown'),
                'original_size' => number_format($originalSize / 1024, 2) . ' KB',
                'original_dimensions' => $originalWidth . 'x' . $originalHeight,
                'compressed_file' => $newFilename,
                'compressed_format' => 'WEBP',
                'compressed_mime' => $savedMimeType,
                'is_webp_verified' => $isWebP ? '✓ YES' : '✗ NO',
                'compressed_size' => number_format($compressedSize / 1024, 2) . ' KB',
                'new_dimensions' => $newWidth . 'x' . $newHeight,
                'quality' => $quality . '%',
                'savings' => $savings . '%',
                'full_path' => $fullNewPath,
                'relative_path' => $newPath
            ]);

            // Alert if not WebP
            if (!$isWebP) {
                Log::warning('⚠️ Saved file is not WebP format!', [
                    'expected' => 'image/webp',
                    'actual' => $savedMimeType,
                    'file' => $newFilename
                ]);
            }

            // Free memory
            imagedestroy($sourceImage);
            imagedestroy($newImage);

            // Delete old file if different format
            if (isset($pathInfo['extension']) && $pathInfo['extension'] !== 'webp') {
                @unlink($fullPath);
                Log::info('🗑️ Deleted original file', ['file' => basename($existingPath)]);
            }

            return $newPath;
        } catch (\Exception $e) {
            Log::error('Failed to compress existing image', [
                'path' => $existingPath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Compress base64 image and return compressed WebP binary data
     *
     * @param string $base64Image Base64 encoded image
     * @param int $quality Quality of compression (0-100)
     * @param int $maxWidth Maximum width of image
     * @return string Binary WebP image data
     */
    public static function compressBase64Image($base64Image, $quality = 80, $maxWidth = 1200)
    {
        // Remove data URI prefix if present
        if (strpos($base64Image, 'data:image') === 0) {
            $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        }

        // Decode base64
        $imageData = base64_decode($base64Image);

        if (!$imageData) {
            throw new \Exception('Invalid base64 image data');
        }

        // Create image from string
        $sourceImage = imagecreatefromstring($imageData);

        if (!$sourceImage) {
            throw new \Exception('Failed to create image from base64 data');
        }

        // Get original dimensions
        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        // Calculate new dimensions
        if ($originalWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = intval(($originalHeight / $originalWidth) * $maxWidth);
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }

        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);

        // Resize
        imagecopyresampled(
            $newImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        // Start output buffering
        ob_start();

        // Convert to WebP
        if (!imagewebp($newImage, null, $quality)) {
            ob_end_clean();
            imagedestroy($sourceImage);
            imagedestroy($newImage);
            throw new \Exception('Failed to convert image to WebP');
        }

        // Get the image data
        $webpData = ob_get_clean();

        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);

        return $webpData;
    }
}
