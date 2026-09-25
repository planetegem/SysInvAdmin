<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class MediumResource extends JsonResource
{
    #[OA\Schema(
        schema: "MediumResource",
        title: "Medium Resource",
        description: "Polymorphic media container holding image or video files or 3D assets",
        type: "object",
        properties: [
            new OA\Property(property: 'id', type: 'integer', example: 1),
            new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-09-14T15:00:00.000000Z'),
            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-09-14T15:00:00.000000Z'),
            new OA\Property(property: 'type', type: 'string', enum: ['none', 'image', 'image-list', 'carousel', 'video', 'gifv', '3d-model'], example: 'image'),
            new OA\Property(
                property: 'files',
                type: 'array',
                description: 'Dynamic array that can contain files of the requested type (image, video or 3D asset)',
                items: new OA\Items(
                    oneOf: [
                        new OA\Schema(ref: '#/components/schemas/ImageResource'),
                        new OA\Schema(ref: '#/components/schemas/VideoResource'),
                        new OA\Schema(ref: '#/components/schemas/Model3DResource')
                    ]
                )
            )
        ]
    )]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'type' => $this->type,
            'files' => $this->resolveFiles()
        ];
    }
    protected function resolveFiles()
    {
        return match ($this->type) {
            'image', 'image-list', 'carousel' => ImageResource::collection($this->whenLoaded('images')),
            'video', 'gifv' => VideoResource::collection($this->whenLoaded('videos')),
            '3d-model' => Model3DResource::collection($this->whenLoaded('models3d')),
            default => [],
        };
    }
}
