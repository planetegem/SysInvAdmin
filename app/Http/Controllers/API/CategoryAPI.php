<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryIndex;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CategoryAPI extends Controller
{
    #[OA\Tag(
        name: 'Categories',
        description: 'Manage inventory categories (used to label items)'
    )]

    // QUERIES
    // 1. Index (the bare minimum)
    #[OA\Get(
        path: '/api/categories/index',
        summary: 'A simple category index, useful for building URLs in the frontend.',
        tags: ['Categories'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of category references',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/CategoryIndex')
                )
            )
        ]
    )]
    public function index()
    {
        return CategoryIndex::collection(Category::get());
    }

    // 2. Get all categories
    #[OA\Get(
        path: '/api/categories/all',
        summary: 'Fetch all categories, including meta data and id\'s of related items',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(
                name: 'includeHidden',
                description: 'Include hidden categories',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean', example: true)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of category references',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/CategoryResource')
                )
            )
        ]
    )]
    public function all(Request $request)
    {
        $includeHidden = filter_var($request->query('includeHidden'), FILTER_VALIDATE_BOOLEAN);

        $categories = Category::with(['items:id,title,slug'])
            ->when(!$includeHidden, function ($query) {
                $query->where(function ($q) {
                    $q->where('hidden', 0)->orWhereNull('hidden');
                });
            })
            ->get();

        return CategoryResource::collection($categories);
    }

    // 3. Get single category with all dependant items
    #[OA\Get(
        path: '/api/categories/{id_type}/{id}',
        summary: 'Fetch a specific category by ID or slug, including all related items',
        tags: ['Categories'],
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
                description: 'The category ID integer or category slug string',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'string',
                    example: '11'
                )
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Order of returned items related to the category',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['creation-ascending', 'creation-descending'],
                    default: 'creation-descending'
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Category details with items tree',
                content: new OA\JsonContent(ref: '#/components/schemas/CategoryResource')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid identifier type provided'
            ),
            new OA\Response(
                response: 404,
                description: 'Category not found'
            )
        ]
    )]

    public function get(Request $request, string $id_type, string $id)
    {
        // Determine item sort direction
        $sortDirection = $request->query('order') === 'creation-ascending' ? 'asc' : 'desc';

        // Determine identifier type
        $column = match (strtolower($id_type)) {
            'id' => 'id',
            'slug' => 'slug',
            default => abort(400, 'Invalid identifier type provided.'),
        };

        // Query the db
        $category = Category::where($column, $id)
            ->with(array_merge(
                [
                    'items' => function ($query) use ($sortDirection) {
                        $query->orderBy('created_at', $sortDirection);
                    }
                ],
                Item::getPrefixedCompanions('items')
            ))
            ->firstOrFail();

        return CategoryResource::makeDetailed($category);
    }
}
