<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Model3D extends Model
{
    // Point to correct table
    protected $table = '3d_models';

    // FIELDS
    // Fillable fields
    protected $fillable = [
        'path',
        'name',
        'alt',
        'mime',
        'size',
        'triangle_count',
        'bounding_box',
        'camera_orbit',
        'camera_target',
        'field_of_view',
        'poster_path',
        'has_animations',
        'animation_names'
    ];

    // Field type casts
    protected $casts = [
        'has_animations' => 'boolean',
        'animation_names' => 'array',
        'bounding_box' => 'array',
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
        static::saving(function (Model3D $model) {
            if ($model->isDirty('path') && !empty($model->path)) {
                $model->setMetadata();
            }
        });
        static::deleting(function (Model3D $model) {
            if (!empty($model->path))
                Storage::disk('uploads')->delete($model->path);
            if (!empty($model->poster))
                Storage::disk('uploads')->delete($model->poster);
        });
    }

    // 2. If path was changed, reset all meta data
    private function setMetadata(): void
    {
        $extension = strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
        $absolute_path = Storage::disk('uploads')->path($this->path);

        if (!file_exists($absolute_path)) {
            return;
        }

        // 1. Common file metadata
        $this->size = filesize($absolute_path);

        // 2. Support GLB (binary)
        if ($extension == 'glb') {
            $this->mime = 'model/gltf-binary';
            $this->parseGlb($absolute_path);
        }

        // 3. Support glTF (text/json)
        if ($extension === 'gltf') {
            $this->mime = 'model/gltf+json';
            $jsonContent = @file_get_contents($absolute_path);
            if ($jsonContent) {
                $jsonData = json_decode($jsonContent, true);
                if (is_array($jsonData)) {
                    $this->extractGltfMetadata($jsonData);
                }
            }
        }
    }

    // 2a. Support for GLB files: extracted to glTF to get meta data
    private function parseGlb(string $filePath): void
    {
        $handle = @fopen($filePath, 'rb');
        if (!$handle) {
            return;
        }

        // 1. Read 12-byte GLB Header
        $header = fread($handle, 12);
        if (strlen($header) < 12) {
            fclose($handle);
            return;
        }

        // Extract magic header as 4-character string (Should be "glTF")
        $magic = substr($header, 0, 4);
        if ($magic !== 'glTF') {
            fclose($handle);
            return;
        }

        // 2. Read First Chunk Header (8 bytes: 4-byte uint32 length + 4-byte chunk type)
        $chunkHeader = fread($handle, 8);
        if (strlen($chunkHeader) < 8) {
            fclose($handle);
            return;
        }

        $chunkData = unpack('Vlength', substr($chunkHeader, 0, 4));
        $chunkLength = $chunkData['length'] ?? 0;
        $chunkType = substr($chunkHeader, 4, 4);

        // Chunk type for JSON in glTF binary is "JSON" (ASCII 0x4E4F534A)
        // We check both raw string and hex representation for safety
        if ($chunkType === 'JSON' || bin2hex($chunkType) === '4a534f4e') {
            if ($chunkLength > 0) {
                $jsonRaw = fread($handle, $chunkLength);
                $jsonData = json_decode($jsonRaw, true);

                if (is_array($jsonData)) {
                    $this->extractGltfMetadata($jsonData);
                }
            }
        }

        fclose($handle);
    }

    // 2b. Support for GLTF
    private function extractGltfMetadata(array $json): void
    {
        // --- 1. Animations ---
        $animations = [];
        if (isset($json['animations']) && is_array($json['animations'])) {
            foreach ($json['animations'] as $anim) {
                if (!empty($anim['name'])) {
                    $animations[] = $anim['name'];
                }
            }
        }
        $this->has_animations = !empty($animations);
        $this->animation_names = $animations;

        // --- 2. Bounding Box & Triangles ---
        $min = [INF, INF, INF];
        $max = [-INF, -INF, -INF];
        $hasPositions = false;
        $triangleCount = 0;

        if (isset($json['meshes']) && is_array($json['meshes'])) {
            foreach ($json['meshes'] as $mesh) {
                if (!isset($mesh['primitives']) || !is_array($mesh['primitives'])) {
                    continue;
                }

                foreach ($mesh['primitives'] as $primitive) {
                    $mode = $primitive['mode'] ?? 4; // Mode 4 = TRIANGLES
                    if ($mode !== 4) {
                        continue;
                    }

                    // --- Calculate Bounding Box strictly from POSITION accessors ---
                    if (isset($primitive['attributes']['POSITION'])) {
                        $posAccessorIdx = $primitive['attributes']['POSITION'];

                        if (isset($json['accessors'][$posAccessorIdx])) {
                            $accessor = $json['accessors'][$posAccessorIdx];

                            if (
                                isset($accessor['min'], $accessor['max']) &&
                                count($accessor['min']) === 3 &&
                                count($accessor['max']) === 3
                            ) {
                                for ($i = 0; $i < 3; $i++) {
                                    $min[$i] = min($min[$i], (float) $accessor['min'][$i]);
                                    $max[$i] = max($max[$i], (float) $accessor['max'][$i]);
                                }
                                $hasPositions = true;
                            }
                        }
                    }

                    // --- Calculate Triangle Count ---
                    if (isset($primitive['indices']) && isset($json['accessors'][$primitive['indices']]['count'])) {
                        $triangleCount += (int) ($json['accessors'][$primitive['indices']]['count'] / 3);
                    } elseif (isset($primitive['attributes']['POSITION']) && isset($json['accessors'][$primitive['attributes']['POSITION']]['count'])) {
                        $triangleCount += (int) ($json['accessors'][$primitive['attributes']['POSITION']]['count'] / 3);
                    }
                }
            }
        }

        if ($hasPositions && $min[0] !== INF) {
            $this->bounding_box = [
                'x' => round(abs($max[0] - $min[0]), 4),
                'y' => round(abs($max[1] - $min[1]), 4),
                'z' => round(abs($max[2] - $min[2]), 4),
            ];
        }

        if ($triangleCount > 0) {
            $this->triangle_count = $triangleCount;
        }
    }

    // FILE PROCESSOR: SAVES VIDEOS TO DRIVE (IF REQUIRED)
    public static function processFile($input)
    {
        // Prepare important props
        $alt = $input['alt'];
        $path = $input['path'];
        $name = $input['name'];
        $poster = $input['poster'];
        $message = "";

        // Move the video from temp storage if required
        if (Str::startsWith($path, 'tmp/')) {
            $newPath = str_replace('tmp/', 'models/', $path);
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
            'alt' => $alt,
            'poster_path' => $poster,
            'message' => $message
        ];
    }
}
