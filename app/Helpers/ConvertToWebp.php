<?php

use Illuminate\Http\UploadedFile;

if (!function_exists('convert_to_webp')) {
    function convert_to_webp(UploadedFile $file, string $outputDir, string $disk, bool $thumbnail = false)
    {
        // Exit if webp isn't supported in PHP install
        if (!function_exists('imagewebp'))
            throw new \Exception(message: "Imagewebp function is not defined");

        // Save as temporary image
        $tempFile = $file->store($outputDir, ['disk' => $disk]);
        $tempPath = Storage::disk($disk)->path($tempFile);

        // Get image info
        $imageInfo = getimagesize($tempPath);
        $mimeType = $imageInfo['mime'];

        // Create image depending on MIME type
        // Wrapped in try/catch statement to catch incorrect sRGB profile exception
        try {
            switch ($mimeType) {
                case 'image/jpeg':

                    $image = imagecreatefromjpeg($tempPath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($tempPath);

                    // Preserve transparency
                    imagealphablending($image, false);
                    imagesavealpha($image, true);

                    break;
                default:
                    throw new \Exception("Unsupported image type: {$mimeType}");
            }
        } catch (Exception $e) {
            // Remove temp file
            Storage::disk(name: $disk)->delete($tempFile);

            // Exit flow
            throw new \Exception($e->getMessage());
        }

        // Convert to webp
        $result = imagewebp($image, "{$tempPath}.webp", 80);

        // Clean up
        imagedestroy($image);
        Storage::disk(name: $disk)->delete($tempFile);

        if (!$result) throw new \Exception(message: "Conversion failed at last step");

        return "{$tempFile}.webp";
    }
}
?>