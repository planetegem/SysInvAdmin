<?php
namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Format;
use Illuminate\Support\Str;
use Imagick;

class ImageOptimizer
{

    public function convertToWebp(string $path, string $disk = 'uploads'): ?string
    {
        // Clean string in case of weird contamination
        $storagePath = str_replace('\\', '/', storage_path());
        $publicPath = str_replace('\\', '/', public_path());

        $cleanPath = str_replace('\\', '/', $path);
        $cleanPath = Str::after($cleanPath, $storagePath);
        $cleanPath = Str::after($cleanPath, $publicPath);
        $cleanPath = Str::after($cleanPath, 'public/storage/');
        $cleanPath = ltrim($cleanPath, '/');

        // If file doesn't exist, return early
        if (!Storage::disk($disk)->exists($cleanPath))
            return null;

        $realPath = Storage::disk($disk)->path($cleanPath);

        // Path details
        $pathInfo = pathinfo($cleanPath);
        $directory = $pathInfo['dirname'] === '.' ? '' : $pathInfo['dirname'] . '/';
        $filename = $pathInfo['filename'];

        // Build new relative path
        $newRelativePath = $directory . $filename . '_' . time() . '.webp';
        $newRelativePath = str_replace('\\', '/', $newRelativePath); // <-- THE FIX

        // Perform conversion
        if (extension_loaded('imagick')) {
            // First choice = Imagick
            $imagick = new Imagick($realPath);
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality(80);

            $imageBytes = $imagick->getImageBlob();

            $imagick->clear();
            $imagick->destroy();

        } else if (extension_loaded('gd')) {
            // Second choice = Intervention with GD driver
            $image = Image::decode($realPath);
            $encodedWebp = $image->encodeUsingFormat(Format::WEBP, quality: 80);
            $imageBytes = (string) $encodedWebp;

        } else {
            // Else throw error
            throw new Exception('WebP conversion failed: Neither Imagick nor GD extensions are enabled on this server.');
        }

        // Save new file & remove old one
        Storage::disk($disk)->put($newRelativePath, $imageBytes);
        Storage::disk($disk)->delete($cleanPath);

        return $newRelativePath;
    }

}


?>