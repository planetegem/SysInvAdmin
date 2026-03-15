<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "SysInvAdmin public API",
    version: "1.0.0",
    description: "This is the API documentation for the SysInvAdmin API."
)]
#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "Local Development (Artisan Serve)"
)]
#[OA\Server(
    url: "https://inventory.planetegem.be",
    description: "Production Server"
)]
#[OA\Tag(
    name: 'Items',
    description: 'Manage inventory items, their updates and possible derivatives'
)]
#[OA\Tag(
    name: 'Categories',
    description: 'Manage inventory categories (used to label items)'
)]

class APIController extends Controller
{
    // ITEMS
    // 1. INDEX
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
                    items: new OA\Items(ref: '#/components/schemas/Index')
                )
            )
        ]
    )]
    public function indexItems()
    {
        return response()->json(ItemGate::index());
    }

    // 2. ALL ITEMS
    #[OA\Get(
        path: '/api/items/all',
        summary: 'Fetch items',
        tags: ['Items'],
        parameters: [
            new OA\Parameter(
                name: 'languages[]',
                description: 'Filter items by language (identified with id). Accepts multiple languages.',
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
                description: 'Filter items by category (identified with id). Accepts multiple categories.',
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
                description: 'Define order in which to return items (descending or ascending, by creation date)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', default: 'descending')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'count', type: 'int', example: 1),
                        new OA\Property(property: 'result', type: 'array', items: new OA\Items(ref: '#/components/schemas/Item'))
                    ]
                )
            )
        ]
    )]
    public function allItems()
    {
        return response()->json(ItemGate::all());
    }

    // 3. SPECIFIC ITEM
    #[OA\Get(
        path: '/api/items/{item_id}',
        summary: 'Fetch sepcific item, based on its internal id',
        tags: ['Items'],
        parameters: [
            new OA\Parameter(
                name: 'item_id',
                description: 'Unique ID of the item',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 101)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Succes',
                content: new OA\JsonContent(ref: '#/components/schemas/Item')
            )
        ]
    )]
    public function getItem($id)
    {
        $item = ItemGate::get($id);
        return response()->json(ItemGate::get($id));
    }

    // CATEGORIES
    // 1. INDEX
    #[OA\Get(
        path: '/api/categories/index',
        summary: 'A simple category index, containing only category id and slug. Useful for building URLs in the frontend.',
        tags: ['Categories'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of category references',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Index')
                )
            )
        ]
    )]
    public function indexCategories()
    {
        return response()->json(CategoryGate::index());
    }

    // 2. ALL CATEGORIES
    #[OA\Get(
        path: '/api/categories/all',
        summary: 'Fetch all categories, including id\'s of related items',
        tags: ['Categories'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of category references',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Category')
                )
            )
        ]
    )]
    public function allCategories()
    {
        return response()->json(CategoryGate::all());
    }

    //3. SPECIFIC CATEGORY
    #[OA\Get(
        path: '/api/categories/{category_id}',
        summary: 'Fetch a specific category with its id, including full tree of all related items',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(
                name: 'category_id',
                description: 'Unique ID of the category',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 101)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of category references',
                content: new OA\JsonContent(ref: '#/components/schemas/Category')
            )
        ]
    )]
    public function getCategory($id)
    {
        return response()->json(CategoryGate::get($id));
    }


    public function getLanguages()
    {
        return response()->json(LanguageGate::all());
    }
    public function getLanguage($id)
    {
        return response()->json(LanguageGate::get($id));
    }


}
