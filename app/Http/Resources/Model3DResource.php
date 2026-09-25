<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class Model3DResource extends JsonResource
{
    #[OA\Schema(
        schema: "Model3DResource",
        title: "3D Model Resource",
        description: "3D assets saved on the server: their paths and meta data (including a path to a poster image).",
        type: "object",
        properties: [
            new OA\Property(property: 'name', type: 'string', example: 'dancing_naked_man'),
            new OA\Property(property: 'path', type: 'string', example: 'https://example.com/storage/models/dancing_naked_man.glb'),
            new OA\Property(property: 'alt', type: 'string', nullable: true, example: '3D render of dancing men'),
            new OA\Property(property: 'poster', type: 'string', example: 'https://example.com/storage/posters/image_of_a_dancing_man.webp'),
            new OA\Property(
                property: 'properties',
                type: 'object',
                properties: [
                    new OA\Property(property: 'mime', type: 'string', example: 'model/gltf-binary'),
                    new OA\Property(property: 'size', type: 'integer', example: 10234),
                    new OA\Property(property: 'triangle_count', type: 'integer', example: 550),
                    new OA\Property(
                        property: 'bounding_box',
                        type: 'object',
                        description: 'Dimensions along X, Y, and Z axes',
                        properties: [
                            new OA\Property(property: 'x', type: 'number', format: 'float', example: 1.2),
                            new OA\Property(property: 'y', type: 'number', format: 'float', example: 2.5),
                            new OA\Property(property: 'z', type: 'number', format: 'float', example: 0.8),
                        ]
                    ),
                    new OA\Property(property: 'has_animations', type: 'boolean', example: false),
                    new OA\Property(
                        property: 'animation_names',
                        type: 'array',
                        nullable: true,
                        items: new OA\Items(type: 'string'),
                        example: ["Walk", "Idle", "Open"]
                    ),
                ]
            )
        ]
    )]
    public function toArray(Request $request): array
    {
        $response = [
            'name' => $this->name,
            'path' => asset("storage/{$this->path}"),
            'alt' => $this->alt,
            'poster' => asset("storage/{$this->poster_path}"),
            'properties' => [
                'mime' => $this->mime,
                'size' => $this->size,
                'triangle_count' => $this->triangle_count,
                'bounding_box' => $this->bounding_box,
                'has_animations' => $this->has_animations,
                'animation_names' => $this->animation_names
            ]
        ];
        return $response;
    }
}
