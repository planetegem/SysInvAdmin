<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CategoryIndex",
    title: "Category Index",
    description: "Bare minimum category representation for URL building",
    type: "object",
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 11),
        new OA\Property(property: 'name', type: 'string', example: 'Het Vleeskanon'),
        new OA\Property(property: 'slug', type: 'string', example: 'het-vleeskanon'),
        new OA\Property(property: 'hidden', type: 'boolean', example: false),
    ]
)]

class CategoryIndex extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'hidden' => (bool) $this->hidden
        ];
    }
}
