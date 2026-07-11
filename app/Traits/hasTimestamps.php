<?php

namespace App\Traits;

trait HasTimestamps
{
    // Helper method to quickly get timestamps
    public function getTimestampsAsString()
    {
        return __(
            'generic.attributes.timestamps.string',
            ['created' => $this->created_at->format('d/m/Y'), 'updated' => $this->updated_at->format('d/m/Y'),]
        );
    }
}