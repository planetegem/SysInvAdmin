<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "MediumIndex",
    title: "Medium Index",
    description: "Index of all media, where a medium is a polymorphic container for media files.",
    type: "object",
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'type', type: 'string', enum: ['image', 'image-list', 'carousel', 'video', 'gifv', '3d-model'], example: 'image'),
    ]
)]

class MediumIndex extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'belongs_to' => $this->resolveBelongsTo(),
        ];
    }
    protected function resolveBelongsTo(): ?string
    {
        if (! $this->mediable_type) {
            return null;
        }

        $class = Relation::getMorphedModel($this->mediable_type) ?? $this->mediable_type;

        return class_basename($class);
    }
}
