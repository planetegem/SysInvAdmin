<?php

namespace App\Traits;

use App\Models\Item;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait SavesMedia
{

    // Validates a media file by 
    // 1. checking if file type is supported
    // 2. attempting to save file
    // (is also called from ItemController)
    public function validateMedia(Request $request, Item $item = new Item())
    {

        switch ($request->file_type_dropdown) {

            // OPTION 1: none selected as media type -> always triggers an update
            case "none":
                return [
                    'head' => 'none',
                    'body' => [
                        'update' => true
                    ]
                ];

            // OPTION 2: image selected as media type (with option to convert to webp)
            case "image":
                // Scenario A: a file was uploaded 
                if ($request->hasFile('media_file')) {
                    $file = $request->file('media_file');
                    if ($request->convert_to_webp) {
                        try {
                            // HAPPY: Convert to webp
                            $path = convert_to_webp($file, 'images', 'uploads');

                        } catch (Exception $e) {
                            // UNHAPPY: Throw error if webp conversion failed
                            return [
                                'head' => 'error',
                                'body' => ['convert_to_webp' => "Conversion to webp failed: {$e->getMessage()}."]
                            ];
                        }

                    } else {
                        // HAPPY: save without converting to webp
                        $path = $file->store('images', ['disk' => 'uploads']);
                    }
                    // Return full response body, with update set to true
                    return [
                        'head' => 'image',
                        'body' => [
                            'file' => $file,
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'alt' => $request->image_alt,
                            'update' => true
                        ]
                    ];

                    // Scenario B: No file upload
                    // HAPPY: If item has same file_type, presume that file was already uploaded (with update set to false)
                } else if ($item->file_type == $request->file_type_dropdown) {
                    return [
                        'head' => 'image',
                        'body' => [
                            'update' => false
                        ]
                    ];
                }
                // UNHAPPY: no file upload, and item doesn't have file attached
                return [
                    'head' => 'error',
                    'body' => ['file_type_dropdown' => "Selected image, but did not provide image file."]
                ];

            // DEFAULT: for everything that isn't implemented yet
            default:
                return [
                    'head' => 'error',
                    'body' => ['file_type_dropdown' => "File type not supported yet."]
                ];
        }
    }

    // Save a media file
    // Takes response from validation as parameter
    // writes from item given as secondary parameter
    public function saveMedia(array $data, Item $item)
    {
        $head = $data['head'];
        $body = $data['body'];

        // If no update required, return early
        if (!$body['update'])
            return;

        // Delete existing media
        if ($item->media->first())
            File::delete(public_path("storage/{$item->media->first()->file_path}"));
        $item->media()->delete();
        
        // Attach new files
        switch ($head) {
            case 'none':
                break;

            case 'image':
                $item->media()->create([
                    'file_type' => $head,
                    'file_path' => $body['path'],
                    'file_name' => $body['name'],
                    'alt' => $body['alt']
                ]);
                break;
        }
        
        // Update item medium type
        $item->update(['file_type' => $head]);
    }
}