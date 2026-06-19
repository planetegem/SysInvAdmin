<?php

namespace App\Traits;

use Exception;
use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasMedia
{

    // VALIDATOR
    // Returns a response object that informs about media validation
    // Happy flow: returns a response object with processed media data
    // Unhappy flow: returns response object with type = error & message = {error_message}
    public static function validateMedia($media)
    {
        // Basic response object returned by method
        $response = [
            'type' => 'none',
            'message' => '',
            'images' => [],
            'safe_urls' => [],
        ];

        switch ($media['type']) {
            // OPTION 1: none selected as type
            case "none":
                return $response;

            // OPTION 2: image (or multiple images) selected as type
            // Has option to convert to webp
            case "image":
            case "carousel":
            case "image-list":

                // Scenario A: files are attached
                if (count($media['path']) > 0) {

                    $response['type'] = $media['type'];

                    // Loop through files and prepare them
                    for ($i = 0; $i < count($media['path']); $i++) {
                        $alt = $media['alt'][$i];
                        $webp = $media['convert_to_webp'][$i];
                        $path = $media['path'][$i];
                        $name = $media['name'][$i];

                        $finalPath = $path;

                        // Convert to webp if required
                        if ($webp != 0) {
                            try {
                                $optimizer = new ImageOptimizer();
                                $finalPath = $optimizer->convertToWebp($path);
                            } catch (Exception $e) {
                                return [
                                    'type' => 'error',
                                    'message' => ['item_media.type' => $e]
                                ];
                            }

                        }

                        // Move from temp storage if required
                        if (Str::startsWith($finalPath, 'tmp/')) {
                            $newPath = str_replace('tmp/', 'images/', $finalPath);
                            Storage::disk('uploads')->move($finalPath, $newPath);
                            $finalPath = $newPath;

                        }
                        $response['images'][] = [
                            'alt' => $alt,
                            'path' => $finalPath,
                            'name' => $name
                        ];
                        $response['safe_urls'][] = $finalPath;

                    }
                    return $response;

                } else {
                    // SCENARIO B: nothing attached
                    return [
                        'type' => 'error',
                        'message' => ['item_media.type' => "Selected image, but did not provide image file."]
                    ];
                }

            // DEFAULT: for everything that isn't implemented yet
            default:
                return [
                    'type' => 'error',
                    'message' => ['item_media.type' => "File type not supported yet."]
                ];
        }
    }

    // Save a media file
    // Takes response from validation as parameter
    // writes from item given as secondary parameter
    public function saveMedia(array $data)
    {
        $type = $data['type'] ?? 'none';
        $images = $data['images'] ?? [];
        $safe_urls = $data['safe_urls'] ?? [];

        // Delete existing media
        foreach ($this->media as $medium) {
            if (Storage::disk('uploads')->exists($medium->file_path) && !in_array($medium->file_path, $safe_urls)) {
                Storage::disk('uploads')->delete($medium->file_path);
            }
            $medium->delete();
        }

        // Attach new files
        switch ($type) {
            case 'none':
                break;

            case 'image':
            case 'carousel':
            case 'image-list':
                foreach ($images as $image) {
                    $this->media()->create([
                        'file_type' => $type,
                        'file_path' => $image['path'],
                        'file_name' => $image['name'],
                        'alt' => $image['alt']
                    ]);
                }
                break;
        }

        // Update item medium type
        $this->update(['file_type' => $type]);
    }

    // Return media as array used in API response
    public function returnMediaAsArray()
    {
        return [
            'type' => $this->file_type,
            'files' => $this->media()->get()->map(fn($medium) => [
                'path' => $medium->file_path,
                'alt' => $medium->alt,
                'original_name' => $medium->file_name,
            ])->toArray()
        ];
    }
}