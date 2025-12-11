<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add custom HEIC/HEIF mime types for validation
        Validator::extendImplicit('heic_image', function ($attribute, $value, $parameters, $validator) {
            if (!$value instanceof \Illuminate\Http\UploadedFile) {
                return false;
            }

            $extension = strtolower($value->getClientOriginalExtension());
            $mimeType = $value->getMimeType();

            // Check if it's HEIC/HEIF
            $isHeic = in_array($extension, ['heic', 'heif']) ||
                      in_array($mimeType, ['image/heic', 'image/heif', 'image/heic-sequence', 'image/heif-sequence', 'application/octet-stream']);

            if ($isHeic) {
                return true;
            }

            // Otherwise check if it's a regular image
            return in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp']);
        });
    }
}
