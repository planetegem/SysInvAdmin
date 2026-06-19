<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    // Upload a temporary image file
    public function uploadTempImage(Request $request)
    {
        // Validate that it's an image file
        $request->validate([
            'image' => 'required|image|max:10240', // max 10MB
        ]);

        // Save file to the tmp folder
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Store file in a temporary folder
            $path = $file->store('tmp', ['disk' => 'uploads']);

            // Return the path to the frontend
            return response()->json(['path' => $path]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
