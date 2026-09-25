<?php

namespace App\Http\Resources;

use App\Traits\HasItemResources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;


#[OA\Schema(
    schema: "CategoryResource",
    title: "Category Resource",
    description: "A category used to label or tag items",
    type: "object",
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 11),
        new OA\Property(property: 'name', type: 'string', example: 'Het Vleeskanon'),
        new OA\Property(property: 'slug', type: 'string', example: 'het-vleeskanon'),
        new OA\Property(property: 'title', type: 'string', nullable: true, example: 'The Meatcannon (all chapters)'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'A short (or absent) description of the category, for public use'),
        new OA\Property(property: 'meta_title', type: 'string', nullable: true, example: 'SEO title of the category'),
        new OA\Property(property: 'meta_description', type: 'string', nullable: true, example: 'SEO description of the category'),
        new OA\Property(property: 'hidden', type: 'boolean', example: true),
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

class CategoryResource extends JsonResource
{
    use HasItemResources;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'hidden' => (bool) $this->hidden,
            'title' => $this->title,
            'description' => $this->description,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'items' => $this->getItems()
        ];
    }
}
