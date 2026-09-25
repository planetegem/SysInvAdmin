<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "LinkResource",
    title: "Link Resource",
    description: "Links to be attached to items. Will become more extensive feature later on.",
    type: "object",
    properties: [
        new OA\Property(property: 'anchor', type: 'string', example: 'Visit the best site ever'),
        new OA\Property(property: 'url', type: 'string', example: 'https://www.planetegem.be')
    ]
)]

class LinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'anchor' => $this->anchor,
            'url' => $this->url,
            'order' => $this->order
        ];
    }
}
