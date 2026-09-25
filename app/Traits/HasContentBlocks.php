<?php

namespace App\Traits;

use App\Models\ContentBlock;

trait HasContentBlocks
{
    // Content Blocks = html enabled description of an item
    // many (content blocks) to 1 (item)
    public function contentBlocks()
    {
        return $this->morphMany(ContentBlock::class, 'contentable');
    }

    // Items will likely only have 1 content block
    public function contentBlock()
    {
        return $this->morphOne(ContentBlock::class, 'contentable');

    }
}