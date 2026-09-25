<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class VideoResource extends JsonResource
{
    #[OA\Schema(
        schema: "VideoResource",
        title: "Video Resource",
        description: "Videos saved on the server: their paths and meta data (including a path to a poster image).",
        type: "object",
        properties: [
            new OA\Property(property: 'name', type: 'string', example: 'dancing_naked_man'),
            new OA\Property(property: 'path', type: 'string', example: 'https://example.com/storage/videos/dancing_naked_man.webm'),
            new OA\Property(property: 'title', type: 'string', nullable: true, example: 'The dance of the naked men'),
            new OA\Property(property: 'description', type: 'string', nullable: true, example: 'What started as an office party, quickly devolved into naked pile of writhing bodies.'),
            new OA\Property(property: 'poster', type: 'string', example: 'https://example.com/storage/posters/image_of_a_dancing_man.webp'),
            new OA\Property(
                property: 'properties',
                type: 'object',
                properties: [
                    new OA\Property(property: 'mime', type: 'string', example: 'video/webm'),
                    new OA\Property(property: 'size', type: 'integer', example: 10234),
                    new OA\Property(property: 'width', type: 'integer', example: 550),
                    new OA\Property(property: 'height', type: 'integer', example: 1000),
                    new OA\Property(property: 'aspect_ratio', type: 'number', example: 0.55),
                    new OA\Property(property: 'duration', type: 'number', example: 6.5),
                    new OA\Property(property: 'has_audio', type: 'boolean', example: false),
                    new OA\Property(property: 'loop', type: 'boolean', example: true),
                ]
            )
        ]
    )]
    public function toArray(Request $request): array
    {
        $response = [
            'name' => $this->name,
            'path' => asset("storage/{$this->path}"),
            'title' => $this->title,
            'description' => $this->description,
            'poster' => asset("storage/{$this->poster_path}"),
            'properties' => [
                'mime' => $this->mime,
                'size' => $this->size,
                'width' => $this->width,
                'height' => $this->height,
                'aspect_ratio' => $this->aspect_ratio,
                'duration' => $this->duration,
                'has_audio' => $this->has_audio,
                'loop' => $this->loop
            ]
        ];
        return $response;
    }
}
