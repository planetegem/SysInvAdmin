<?php

namespace App\Traits;

use App\Models\Medium;

trait HasMedia
{
    // Save a media file
    // Takes response from validation as parameter
    // writes from item given as secondary parameter
    public function saveMedium(array $data)
    {
        // Prepare type
        $type = $data['type'] ?? 'none';

        // If type is none, delete the medium
        // Deleting also means that related files are deleted
        if ($type === 'none') {
            if ($this->medium) {
                $this->medium->delete();
            }
            return;
        }

        // Fetch or create a medium
        $medium = $this->medium()->firstOrNew();

        // Apply type: if types changes, model automatically deletes all files
        $medium->type = $type;
        $medium->save();

        // Attach files        
        $medium->resolveFiles($data);
    }

    // Media: many (media) to 1 (item)
    // Polymorphic relationship
    public function media()
    {
        return $this->morphMany(Medium::class, 'mediable');
    }

    // If you're sure there is only 1 medium, you can also call medium
    public function medium()
    {
        return $this->morphOne(Medium::class, 'mediable');
    }
}