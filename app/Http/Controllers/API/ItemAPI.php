<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemIndex;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Traits\HasFilters;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Items',
    description: 'Manage inventory items, their updates and possible derivatives'
)]

class ItemAPI extends Controller
{
    use HasFilters;

    // QUERIES
    // 1. Index (the bare minimum)
    #[OA\Get(
        path: '/api/items/index',
        summary: 'A simple item index, containing only the item id and slug. Useful to build URLs in the frontend.',
        tags: ['Items'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of item references',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/ItemIndex')
                )
            )
        ]
    )]
    public function index()
    {
        return ItemIndex::collection(Item::get());
    }

    // 2. Get all items, with optional parameters to filter by language or category id
    #[OA\Get(
        path: '/api/items/all',
        summary: 'Fetch items',
        tags: ['Items'],
        parameters: [
            new OA\Parameter(
                name: 'languages[]',
                description: 'Filter items by language (short name). Accepts multiple languages (OR).',
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
                name: 'language_ids[]',
                description: 'Filter items by language (id). Accepts multiple languages (OR).',
                in: 'query',
                required: false,
                style: 'form',
                explode: true,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'integer')
                )
            ),
            new OA\Parameter(
                name: 'categories[]',
                description: 'Filter items by category (identified with slug). Accepts multiple categories (OR).',
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
                name: 'category_ids[]',
                description: 'Filter items by category (identified with id). Accepts multiple categories (OR).',
                in: 'query',
                required: false,
                style: 'form',
                explode: true,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'integer')
                )
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Define order which items are returned (descending or ascending, by creation date)',
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
                            items: new OA\Items(ref: '#/components/schemas/ItemResource')
                        )
                    ]
                )
            )
        ]
    )]
    public function all(Request $request)
    {
        // Isolate request parameters
        $languages = (array) $request->input('languages', []);
        $language_ids = (array) $request->input('language_ids', []);
        $categories = (array) $request->input('categories', []);
        $category_ids = (array) $request->input('category_ids', []);

        // Build query step by step
        $query = Item::withCompanions();

        // Apply language and category filter
        $this->filterLanguages($query, $languages, $language_ids);
        $this->filterCategories($query, $categories, $category_ids);

        // Determine order (descending or ascending)
        $sortDirection = $request->input('order') == 'ascending' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sortDirection);

        // Paginate results
        $per_page = $request->input('per_page', 20);
        $paginatedItems = $query->paginate($per_page);

        // Wrap into object with meta data
        $response = [
            'count' => $paginatedItems->total(),
            'current_page' => $paginatedItems->currentPage(),
            'last_page' => $paginatedItems->lastPage(),
            'per_page' => $paginatedItems->perPage(),
            'result' => ItemResource::collection($paginatedItems)
        ];
        return $response;
    }

    // 3. Get single item (by id or slug)
    #[OA\Get(
        path: '/api/items/id/{item_id}',
        summary: 'Fetch specific item, based on its internal id',
        tags: ['Items'],
        parameters: [
            new OA\Parameter(
                name: 'id_type',
                description: 'Type of identifier provided in the URL path',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['id', 'slug'],
                    example: 'id'
                )
            ),
            new OA\Parameter(
                name: 'id',
                description: 'The item ID integer or item slug string',
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
                content: new OA\JsonContent(ref: '#/components/schemas/ItemResource')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid identifier type provided'
            ),
            new OA\Response(
                response: 404,
                description: 'Item not found',
            )
        ]
    )]
    public function get(Request $request, string $id_type, string $id)
    {
        // Determine identifier type
        $column = match (strtolower($id_type)) {
            'id' => 'id',
            'slug' => 'slug',
            default => abort(400, 'Invalid identifier type provided.'),
        };

        $item = Item::where($column, $id)->with(
            array_merge(
                Item::getBaseCompanions(),
                Item::getPrefixedCompanions('children'),
                Item::getPrefixedCompanions('parents')
            )
        )->firstOrFail();

        return new ItemResource($item);
    }
}
