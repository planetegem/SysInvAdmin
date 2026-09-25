<?php

namespace App\Traits;

use App\Http\Resources\ItemIndex;
use App\Http\Resources\ItemResource;

// Trait to be used in resources that have items as a property (CategoryResource, LanguageResource, ...)
trait HasItemResources
{
    // Toggle How much of an item is shown
    protected bool $showDetailedItems = false;
    public function withDetailedItems(bool $withDetailedItems = true): static
    {
        $this->showDetailedItems = $withDetailedItems;
        return $this;
    }

    // Staic helper to create the detailed variant
    public static function makeDetailed(mixed $resource): static
    {
        $instance = static::make($resource);
        $instance->showDetailedItems = true;

        return $instance;
    }

    // Create the items property
    protected function getItems()
    {
        return $this->whenLoaded('items', function () {
            if ($this->items->isEmpty()) {
                return [];
            }
            return $this->showDetailedItems
                ? ItemResource::collection($this->items)
                : ItemIndex::collection($this->items);
        });
    }


}