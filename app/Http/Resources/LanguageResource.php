<?php

namespace App\Http\Resources;

use App\Traits\HasItemResources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "LanguageResource",
    title: "Language Resource",
    description: "A Language that something is expressed in. Can have items attached to it. Querying a language reveals all items in that language.",
    type: "object",
    properties: [
        new OA\Property(property: 'id', type: 'int', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'French'),
        new OA\Property(property: 'short_name', type: 'string', example: 'fr'),
        new OA\Property(property: 'native_name', type: 'string', example: 'français'),
        new OA\Property(
            property: 'items',
            oneOf: [
                new OA\Schema(
                    type: 'array',
                    description: 'Array of simplified Item objects (purely to construct links)',
                    items: new OA\Items(ref: "#/components/schemas/ItemIndex")
                ),
                new OA\Schema(
                    type: 'array',
                    description: 'Array of detailed Item objects (when fetching a single category)',
                    items: new OA\Items(ref: "#/components/schemas/ItemResource")
                )
            ]
        ),
    ]
)]

class LanguageResource extends JsonResource
{
    use HasItemResources;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'native_name' => $this->native_name,
            'items' => $this->getItems()
        ];
    }
}
