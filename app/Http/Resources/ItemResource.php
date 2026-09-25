<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ItemResource',
    title: 'Item Resource',
    description: 'Represents an individual item along with its conditionally loaded relationships and media.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-09-14T15:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-09-14T15:00:00.000000Z'),
        new OA\Property(property: 'title', type: 'string', example: 'Mangerie Online, version 2'),
        new OA\Property(property: 'slug', type: 'string', example: 'mangerie-online-version-2'),
        new OA\Property(
            property: 'description',
            type: 'string',
            nullable: true,
            description: 'Rendered content block string, can contain HTML.',
            example: '<p>This is the item description.</p>'
        ),
        new OA\Property(
            property: 'language',
            ref: '#/components/schemas/LanguageResource',
            nullable: true,
            description: 'Present only when language relation is loaded.'
        ),
        new OA\Property(
            property: 'media',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/MediumResource'),
            nullable: true,
            description: 'Present only when media relation is loaded.'
        ),
        new OA\Property(
            property: 'categories',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/CategoryIndex'),
            nullable: true,
            description: 'Present only when categories relation is loaded.'
        ),
        new OA\Property(
            property: 'links',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/LinkResource'),
            nullable: true,
            description: 'Present only when links relation is loaded.'
        ),
        new OA\Property(
            property: 'relationships',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/ItemRelationship'),
            nullable: true,
            description: 'Omitted entirely if relations are not loaded or if no relationships exist.'
        )
    ]
)]

class ItemResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return array_merge([
            'id' => $this->id,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'title' => $this->title,
            'description' => $this->whenLoaded('contentBlock', function () {
                return $this->contentBlock?->content;
            }),
            'language' => new LanguageResource($this->whenLoaded('language')),
            'media' => MediumResource::collection($this->whenLoaded('media')),
            'categories' => CategoryIndex::collection($this->whenLoaded('categories')),
            'links' => LinkResource::collection($this->whenLoaded('links')),
            'relationships' => $this->when(
                $this->relationLoaded('parents') && $this->relationLoaded('children'),
                function () {
                    $relationships = $this->relationships();
                    return !empty($relationships) ? $relationships : null;
                }
            ),
        ], $this->getRelationshipsData());
    }

    // Helper method to get relationships
    protected function getRelationshipsData(): array
    {
        // Check if both relations are loaded
        if (!$this->relationLoaded('parents') || !$this->relationLoaded('children')) {
            return [];
        }

        $relationships = $this->relationships();

        // If empty, return empty array so nothing is merged into the Resource output
        if (empty($relationships)) {
            return [];
        }

        return [
            'relationships' => ItemRelationshipResource::collection($relationships),
        ];
    }
}
