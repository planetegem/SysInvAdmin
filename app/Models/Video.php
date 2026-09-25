<?php

namespace App\Models;

use getID3;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Video extends Model
{
    // FIELDS
    // Fillable fields
    protected $fillable = [
        'path',
        'name',
        'title',
        'description',
        'mime',
        'width',
        'height',
        'aspect_ratio',
        'size',
        'duration',
        'has_audio',
        'loop',
        'poster_path'
    ];

    // Field type casts
    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size' => 'integer',
        'aspect_ratio' => 'float:4',
        'duration' => 'float:2',
        'has_audio' => 'boolean',
        'loop' => 'boolean'
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
        static::saving(function (Video $video) {
            if ($video->isDirty('path') && !empty($video->path)) {
                $video->setMetadata();
            }
        });
        static::deleting(function (Video $video) {
            if (!empty($video->path))
                Storage::disk('uploads')->delete($video->path);
            if (!empty($video->poster))
                Storage::disk('uploads')->delete($video->poster);
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

        // Use getid3 to get file width and height
        $getID3 = new getID3();
        $fileinfo = $getID3->analyze($absolute_path);

        $width = $fileinfo['video']['resolution_x'] ?? null;
        $height = $fileinfo['video']['resolution_y'] ?? null;

        $this->width = $width;
        $this->height = $height;

        // Calculate aspect ratio if possible
        $this->aspect_ratio = ($width && $height) ? round($width / $height, 4) : null;

        // Get other important details
        $this->duration = $fileinfo['playtime_seconds'] ?? null;
        $this->mime = $this->resolveMimeType($absolute_path, $fileinfo);
    }

    // Resolver for mime type in case getid3 fails
    private function resolveMimeType(string $absolutePath, array $fileInfo): string
    {
        // 1. Use getid3's parsed MIME type if present
        if (!empty($fileInfo['mime_type']))
            return $fileInfo['mime_type'];

        // 2. Fall back to native PHP mime_content_type()
        $nativeMime = @mime_content_type($absolutePath);
        if ($nativeMime && $nativeMime !== 'application/octet-stream') {
            return $nativeMime;
        }

        // 3. Fall back to simple extension mapping
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'mp4', 'm4v' => 'video/mp4',
            'webm' => 'video/webm',
            'ogv', 'ogg' => 'video/ogg',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'mkv' => 'video/x-matroska',
            'flv' => 'video/x-flv',
            default => 'application/octet-stream',
        };
    }

    // FILE PROCESSOR: SAVES VIDEOS TO DRIVE (IF REQUIRED)
    public static function processFile($input)
    {
        // Prepare important props
        $title = $input['title'];
        $description = $input['description'];
        $include_audio = $input['include_audio'];
        $path = $input['path'];
        $name = $input['name'];
        $poster = $input['poster'];
        $message = "";

        // Move the video from temp storage if required
        if (Str::startsWith($path, 'tmp/')) {
            $newPath = str_replace('tmp/', 'videos/', $path);
            Storage::disk('uploads')->move($path, $newPath);
            $path = $newPath;
        }
        // Move the poster from temp storage if required
        if (Str::startsWith($poster, 'tmp/')) {
            $newPoster = str_replace('tmp/', 'posters/', $poster);
            Storage::disk('uploads')->move($poster, $newPoster);
            $poster = $newPoster;
        }

        return [
            'name' => pathinfo($name, PATHINFO_FILENAME),
            'path' => $path,
            'title' => $title,
            'description' => $description,
            'has_audio' => $include_audio ? 1 : 0,
            'poster_path' => $poster,
            'message' => $message
        ];
    }
}