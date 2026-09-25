<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class ImageResource extends JsonResource
{
    #[OA\Schema(
        schema: "ImageResource",
        title: "Image Resource",
        description: "Images saved on the server: their paths and meta data.",
        type: "object",
        properties: [
            new OA\Property(property: 'name', type: 'string', example: 'naked_man'),
            new OA\Property(property: 'path', type: 'string', example: 'https://example.com/storage/images/image_of_naked_man.webp'),
            new OA\Property(property: 'alt', type: 'string', nullable: true, example: 'An image of a naked man'),
            new OA\Property(
                property: 'properties',
                type: 'object',
                properties: [
                    new OA\Property(property: 'mime', type: 'string', example: 'image/webp'),
                    new OA\Property(property: 'size', type: 'integer', example: 10234),
                    new OA\Property(property: 'width', type: 'integer', example: 550),
                    new OA\Property(property: 'height', type: 'integer', example: 1000),
                    new OA\Property(property: 'aspect_ratio', type: 'number', example: 0.55)
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
            'properties' => [
                'mime' => $this->mime,
                'size' => $this->size,
                'width' => $this->width,
                'height' => $this->height,
                'aspect_ratio' => $this->aspect_ratio
            ]
        ];

        return $response;
    }
}
