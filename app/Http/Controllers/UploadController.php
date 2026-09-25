<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UploadController extends Controller
{
    // Upload a temporary file
    public function uploadTempFile(Request $request)
    {
        // Validate the file: can be an image, video or model
        $request->validate([
            'type' => ['required', 'string', Rule::in('image', 'video', 'model')],
            'file' => [
                'required',
                'file',
                Rule::when($request->type === 'image', [
                    'image',
                    'mimes:jpeg,png,jpg,gif,webp',
                    'max:10240', // 10MB limit
                ]),
                // Validate as Video
                Rule::when($request->type === 'video', [
                    'mimes:mp4,mov,avi,wmv,webm',
                    'max:10240', // 10MB limit
                ]),
                // Validate as 3D Model
                Rule::when($request->type === 'model', [
                    'mimes:stl,obj,gltf,glb,fbx',
                    'max:10240', // 10MB limit
                ])
            ],
            // Optional poster image validation (mainly used when type === 'video')
            'poster' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120', // 5MB limit
            ],
        ]);

        // Save file to the tmp folder
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Store file in a temporary folder
            $filePath = $file->store('tmp', ['disk' => 'uploads']);

            $posterPath = null;
            if ($request->hasFile('poster')) {
                $posterPath = $request->file('poster')->store('tmp', ['disk' => 'uploads']);
            }

            // Return the path to the frontend
            return response()->json(['path' => $filePath, 'poster' => $posterPath]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
