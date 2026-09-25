<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediumIndex;
use App\Http\Resources\MediumResource;
use App\Models\Medium;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MediaAPI extends Controller
{
    #[OA\Tag(
        name: 'Media',
        description: 'Elements from the media library; typically attached to items, but can also exist independantly.'
    )]

    // QUERIES
    // 1. Index (the bare minimum)
    #[OA\Get(
        path: '/api/media/index',
        summary: 'A simple media index. Tells you the tpe of a medium and whether its attached to anything.',
        tags: ['Media'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of media references',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/MediumIndex')
                )
            )
        ]
    )]
    public function index()
    {
        return MediumIndex::collection(Medium::get());
    }

    // 2. Fetch all (with pagination and filtering by type)
    #[OA\Get(
        path: '/api/media/all',
        summary: 'Fetch media',
        tags: ['Media'],
        parameters: [
            new OA\Parameter(
                name: 'types[]',
                description: 'Filter items by type. Accepts multiple types (OR).',
                in: 'query',
                required: false,
                style: 'form',
                explode: true,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'string')
                )
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Define order which media are returned (descending or ascending, by creation date)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', default: 'descending')
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Page number for pagination',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\Parameter(
                name: 'per_page',
                description: 'Number of items per page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 15)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'count', type: 'integer', example: 42),
                        new OA\Property(property: 'current_page', type: 'integer', example: 1),
                        new OA\Property(property: 'last_page', type: 'integer', example: 3),
                        new OA\Property(property: 'per_page', type: 'integer', example: 15),
                        new OA\Property(
                            property: 'result',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/MediumResource')
                        )
                    ]
                )
            )
        ]
    )]
    public function all(Request $request)
    {
        // Isolate request parameters
        $types = (array) $request->input('types', []);
        $sortDirection = $request->input('order') == 'ascending' ? 'asc' : 'desc';

        // Build query step by step
        $query = Medium::withFiles()
            ->when(!empty($types), fn ($query) => $query->whereIn('type', $types))
            ->orderBy('created_at', $sortDirection);

        // Paginate results
        $per_page = $request->input('per_page', 20);
        $paginatedItems = $query->paginate($per_page);

        // Wrap into object with meta data
        $response = [
            'count' => $paginatedItems->total(),
            'current_page' => $paginatedItems->currentPage(),
            'last_page' => $paginatedItems->lastPage(),
            'per_page' => $paginatedItems->perPage(),
            'result' => MediumResource::collection($paginatedItems)
        ];
        return $response;
    }

    // 3. Fetch individual media item
        #[OA\Get(
        path: '/api/media/id/{id}',
        summary: 'Fetch specific medium, based on its internal id',
        tags: ['Media'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The medium ID integer',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'string',
                    example: '11'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Succes',
                content: new OA\JsonContent(ref: '#/components/schemas/MediumResource')
            ),
            new OA\Response(
                response: 404,
                description: 'Media item not found',
            )
        ]
    )]
    public function get(Request $request, string $id)
    {
        $medium = Medium::where('id', $id)->withFiles()->firstOrFail();
        return new MediumResource($medium);
    }
}
