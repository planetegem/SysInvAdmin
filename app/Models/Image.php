<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use Exception;
use App\Services\ImageOptimizer;
use Illuminate\Support\Str;

class Image extends Model
{
    // FIELDS
    // Fillable fields
    protected $fillable = [
        'path',
        'name',
        'mime',
        'alt',
        'width',
        'height',
        'aspect_ratio',
        'size',
        'clip_type',
        'clip_path',
        'placeholder',
        'variants'
    ];

    // Field type casts
    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size' => 'integer',
        'aspect_ratio' => 'float:4',
        'clip_path' => 'array',
        'variants' => 'array'
    ];

    // PROPERTIES
    // Find all media the image is attached to
    public function media()
    {
        return $this->belongsToMany(Medium::class);
    }

    // ON BOOT
    // 1. Boot method: check if path was changed
    public static function booted(): void
    {
        static::saving(function (Image $image) {
            if ($image->isDirty('path') && !empty($image->path)) {
                $image->setMetadata();
            }
        });
        static::deleting(function (Image $image) {
            if (!empty($image->path))
                Storage::disk('uploads')->delete($image->path);
        });
    }

    // 2. If path was changed, reset all meta data
    private function setMetadata(): void
    {

        // Get the extension to know how to read the file
        $extension = pathinfo($this->path, PATHINFO_EXTENSION);

        // Get absolute path to the file
        $absolute_path = Storage::disk('uploads')->path($this->path);

        // Return early if file not found
        if (!file_exists($absolute_path))
            return;

        // Set file size in bytes
        $this->size = filesize($absolute_path);

        // 1. Handle SVG files
        if (strtolower($extension) === 'svg') {
            // Manually set mime type
            $this->mime = 'image/svg+xml';

            // Read xml to determine width, height and aspect ratio
            $xml = @simplexml_load_file($absolute_path);
            if ($xml !== false) {
                $attributes = $xml->attributes();

                $width = (float) $attributes->width;
                $height = (float) $attributes->height;

                // Fallback: if explicit reading of width/height fails, analyze viewbox
                if ((!$width || !$height) && isset($attributes->viewBox)) {
                    $viewBox = explode(' ', (string) $attributes->viewBox);
                    if (count($viewBox) === 4) {
                        $width = (float) $viewBox[2];
                        $height = (float) $viewBox[3];
                    }
                }

                // Set the width, height & aspect ratio props
                if ($width > 0 && $height > 0) {
                    $this->width = (int) round($width);
                    $this->height = (int) round($height);
                    $this->aspect_ratio = round($width / $height, 4);
                }
            }

        }

        // 2. Handle standard rasterized images (jpg, png, webp)
        // Get image data
        $imageData = @getimagesize($absolute_path);
        if ($imageData === false)
            return;

        // Set width and height
        [$width, $height] = $imageData;
        $this->width = $width;
        $this->height = $height;

        // Calculate aspect ratio
        if ($width > 0 && $height > 0) {
            $this->aspect_ratio = round($width / $height, 4);
        }

        // Get mime type
        $this->mime = $imageData['mime'];
    }

    // FILE PROCESSOR: SAVES IMAGES TO DRIVE (IF REQUIRED)
    public static function processFile($input)
    {
        // Prepare important props
        $alt = $input['alt'];
        $webp = $input['convert_to_webp'];
        $path = $input['path'];
        $name = $input['name'];
        $message = "";

        // Convert to webp if required
        if ($webp != 0) {
            // Wrap in try to catch possible conversion errors
            try {
                $optimizer = new ImageOptimizer();
                $path = $optimizer->convertToWebp($path);
            } catch (Exception $e) {
                $message = ['item_media.type' => $e];
            }
        }

        // Move from temp storage if required
        if (Str::startsWith($path, 'tmp/')) {
            $newPath = str_replace('tmp/', 'images/', $path);
            Storage::disk('uploads')->move($path, $newPath);
            $path = $newPath;

        }
        return [
            'name' => pathinfo($name, PATHINFO_FILENAME),
            'path' => $path,
            'alt' => $alt,
            'message' => $message
        ];
    }
}
