<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ItemRelationship',
    title: 'Item Relationship Resource',
    properties: [
        new OA\Property(
            property: 'relationship',
            type: 'object',
            properties: [
                new OA\Property(property: 'label', type: 'string', example: 'parent_of'),
                new OA\Property(property: 'descriptor', type: 'string', example: 'is a parent of')
            ]
        ),
        new OA\Property(property: 'item', type: 'integer', example: 22)
    ]
)]
class ItemRelationshipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $rel = $this['relationship'];

        return [
            'relationship' => [
                'label' => $rel->label,
                'descriptor' => $rel->descriptor,
            ],
            'item' => new ItemResource($this['item']),
        ];
    }
}
