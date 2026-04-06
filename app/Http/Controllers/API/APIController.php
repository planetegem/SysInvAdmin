<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Exception;
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
#[OA\Tag(
    name: 'Languages',
    description: 'Manage inventory items, based filtered by language'
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
                description: 'Filter items by language (short name). Accepts multiple languages.',
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
                name: 'languages_ids[]',
                description: 'Filter items by language (id). Accepts multiple languages.',
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
                description: 'Filter items by category (identified with slug). Accepts multiple categories.',
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
    // 3a. BY ID
    #[OA\Get(
        path: '/api/items/id/{item_id}',
        summary: 'Fetch specific item, based on its internal id',
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
            ),
            new OA\Response(
                response: 404,
                description: 'Item not found',
            )
        ]
    )]
    public function getItemById($id)
    {
        try {
            return response()->json(ItemGate::get(intval($id)));
        } catch (Exception $e) {
            return response(null, 404);
        }
    }

    // 3b. BY SLUG
    #[OA\Get(
        path: '/api/items/slug/{item_slug}',
        summary: 'Fetch specific item, based on its slug (unique)',
        tags: ['Items'],
        parameters: [
            new OA\Parameter(
                name: 'item_slug',
                description: 'Slug of the item',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'het-vleeskanon-van-clementijn')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Succes',
                content: new OA\JsonContent(ref: '#/components/schemas/Item')
            ),
            new OA\Response(
                response: 404,
                description: 'Item not found',
            )
        ]
    )]
    public function getItemBySlug($slug)
    {
        try {
            return response()->json(ItemGate::get($slug));
        } catch (Exception $e) {
            return response(null, 404);
        }
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

    // 3. SPECIFIC CATEGORY
    // 3a. BY ID
    #[OA\Get(
        path: '/api/categories/id/{category_id}',
        summary: 'Fetch a specific category with its id, including full tree of all related items',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(
                name: 'category_id',
                description: 'Unique ID of the category',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 101)
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Define order in which to return items related to the category. Possible orders: creation-ascending, creation-descending.',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', default: 'creation-descending')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of category references',
                content: new OA\JsonContent(ref: '#/components/schemas/Category')
            ),
            new OA\Response(
                response: 404,
                description: 'Category not found',
            )
        ]
    )]
    public function getCategoryById($id)
    {
        try {
            return response()->json(CategoryGate::get(intval($id)));
        } catch (Exception $e) {
            return response(null, 404);
        }
    }

    // 3b. BY SLUG
    #[OA\Get(
        path: '/api/categories/slug/{category_slug}',
        summary: 'Fetch a specific category with its slug, including full tree of all related items',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(
                name: 'category_slug',
                description: 'Unique slug of the category',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'het-vleeskanon')
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Define order in which to return items related to the category. Possible orders: creation-ascending, creation-descending.',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', default: 'creation-descending')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of category references',
                content: new OA\JsonContent(ref: '#/components/schemas/Category')
            ),
            new OA\Response(
                response: 404,
                description: 'Category not found',
            )
        ]
    )]
    public function getCategoryBySlug($slug)
    {
        try {
            return response()->json(CategoryGate::get($slug));
        } catch (Exception $e) {
            return response(null, 404);
        }
    }

    // LANGUAGES
    // 1. ALL LANGUAGES
    #[OA\Get(
        path: '/api/languages/all',
        summary: 'Fetch all languages, including id\'s of related items',
        tags: ['Languages'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of language references',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Language')
                )
            )
        ]
    )]
    public function allLanguages()
    {
        return response()->json(LanguageGate::all());
    }

    // 2. SPECIFIC LANGUAGE
    // 2a. BY ID
    #[OA\Get(
        path: '/api/languages/id/{language_id}',
        summary: 'Fetch a specific language with its id, including full tree of all related items',
        tags: ['Languages'],
        parameters: [
            new OA\Parameter(
                name: 'language_id',
                description: 'Unique ID of the language',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Define order in which to return items related to the language. Possible orders: creation-ascending, creation-descending.',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', default: 'creation-descending')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of language references',
                content: new OA\JsonContent(ref: '#/components/schemas/Language')
            ),
            new OA\Response(
                response: 404,
                description: 'Language not found',
            )
        ]
    )]

    public function getLanguageById($id)
    {
        try {
            return response()->json(LanguageGate::get(intval($id)));
        } catch (Exception $e) {
            return response(null, 404);
        }
    }

    // 3b. BY SHORT NAME
    #[OA\Get(
        path: '/api/languages/name/{language_short_name}',
        summary: 'Fetch a specific language with its unique short name, including full tree of all related items',
        tags: ['Languages'],
        parameters: [
            new OA\Parameter(
                name: 'language_short_name',
                description: 'Unique short name of the language',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'en')
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Define order in which to return items related to the language. Possible orders: creation-ascending, creation-descending.',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', default: 'creation-descending')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of language references',
                content: new OA\JsonContent(ref: '#/components/schemas/Language')
            ),
            new OA\Response(
                response: 404,
                description: 'Language not found',
            )
        ]
    )]
    public function getLanguageByShortName($short)
    {
        try {
            return response()->json(LanguageGate::get($short));
        } catch (Exception $e) {
            return response(null, 404);
        }
    }

}
