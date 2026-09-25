<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

// Return the item id, slug and name: 
// exactly enough to create a link to an item
#[OA\Schema(
    schema: 'ItemIndex',
    title: 'Item Index',
    description: 'Return the item id, slug and name, exactly enough to create a link to an individual item.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'Mangerie Online, version 2'),
        new OA\Property(property: 'slug', type: 'string', example: 'mangerie-online-version-2')
    ]
)]

class ItemIndex extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title
        ];
    }
}
