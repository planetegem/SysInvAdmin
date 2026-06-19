<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Link;
use OpenApi\Attributes as OA;


class ItemGate extends Controller
{
    // QUERY HELPERS
    // 1. Return companions (for eager loading)
    public static function getItemCompanions($included = ['description', 'categories', 'links', 'media', 'languages'])
    {
        $companions = [];
        if (in_array('description', $included))
            $companions[] = 'contentBlocks:id,content';

        if (in_array('categories', $included))
            $companions[] = 'categories:id,name,slug,hidden';

        if (in_array('links', $included))
            $companions[] = 'links:id,item_id,anchor,url';

        if (in_array('media', $included))
            $companions[] = 'media:id,item_id,file_name,file_path,alt';

        if (in_array('languages', $included))
            $companions[] = 'language:id,name,short_name,native_name';

        return $companions;
    }

    // RESPONSE HELPERS
    // 1. OpenAPI schema objects
    #[OA\Schema(
        schema: "Index",
        title: "Index",
        description: "Index containing id and slug, useful for building URL's",
        type: "object",
        properties: [
            new OA\Property(property: 'id', type: 'int', example: 15),
            new OA\Property(property: 'slug', type: 'string', example: 'example-of-index-item')
        ]
    )]
    #[OA\Schema(
        schema: "Link",
        title: "Link",
        description: "",
        type: "object",
        properties: [
            new OA\Property(property: 'anchor', type: 'string', example: 'visit the best site ever'),
            new OA\Property(property: 'url', type: 'string', example: 'https://www.planetegem.be')
        ]
    )]
    #[OA\Schema(
        schema: "Media",
        title: "Media",
        description: "Media attached to other instances, with links to relevant files",
        type: "object",
        properties: [
            new OA\Property(property: 'type', type: 'string', example: 'thumbnail'),
            new OA\Property(
                property: 'files',
                type: 'array',
                items: new OA\Items(
                    type: "object",
                    properties: [
                        new OA\Property(property: 'path', type: 'string', example: 'images/image.webp'),
                        new OA\Property(property: 'alt', type: 'string', example: 'An example image'),
                        new OA\Property(property: 'original_name', type: 'string', example: 'private_name.png')
                    ]
                )
            )
        ]
    )]
    #[OA\Schema(
        schema: "Item",
        title: "Item",
        description: "An item from the inventory",
        type: "object",
        properties: [
            new OA\Property(property: 'id', type: 'int', example: 11),
            new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2025-03-16T00:00:00.000000Z'),
            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2025-03-16T00:00:00.000000Z'),
            new OA\Property(property: 'title', type: 'string', example: 'Mangerie Online, version 2'),
            new OA\Property(property: 'slug', type: 'string', example: 'mangerie-online-version-2'),
            new OA\Property(property: 'description', type: 'html', example: '<p>A short description</p>'),
            new OA\Property(property: 'type', type: 'string', example: 'update'),
            new OA\Property(property: 'language', ref: "#/components/schemas/Language"),
            new OA\Property(property: 'media', ref: "#/components/schemas/Media"),
            new OA\Property(property: 'categories', type: 'array', items: new OA\Items(ref: "#/components/schemas/Category")),
            new OA\Property(property: 'links', type: 'array', items: new OA\Items(ref: "#/components/schemas/Link")),
            new OA\Property(property: 'relationships', type: 'array', items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'relationship', type: 'string', example: 'update'),
                    new OA\Property(property: 'item', ref: '#/components/schemas/Item')
                ]
            ))
        ]
    )]

    // 2. Create item response object (use array in paramter to determine field blocks to include)
    // 2a. Helper function that creates the medium object
    public static function stitchMedia(Item $item)
    {
        // OBSOLETE
    }

    // 2b. Create item object (with include array to specify which blocks to add - default = all blocks)
    public static function stitchItem($item, $include = ['description', 'media', 'categories', 'links', 'relationships'])
    {
        $object = [
            'id' => $item->id,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
            'title' => $item->title,
            'slug' => $item->slug,
            'type' => $item->type,
            'language' => $item->language,
        ];
        if (in_array('description', $include))
            $object['description'] = $item->contentBlocks()->first()->content;

        if (in_array('media', $include) && $item->file_type)
            $object['media'] = $item->returnMediaAsArray();

        if (in_array('categories', $include) && $item->categories->count() > 0)
            $object['categories'] = $item->categories->map(function (Category $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'hidden' => $category->hidden == 0 ? false : true
                ];
            });

        if (in_array('links', $include) && $item->links->count() > 0)
            $object['links'] = $item->links->map(function (Link $link) {
                return [
                    'anchor' => $link->anchor,
                    'url' => $link->url
                ];
            });

        if (in_array('relationships', $include)) {
            $object['relationships'] = array_map(function ($relationship) {
                $relatedItem = Item::where('id', $relationship['item'])->with(ItemGate::getItemCompanions(['media', 'languages', 'links', 'categories']))->first();
                return [
                    'relationship' => $relationship['relationship'],
                    'item' => ItemGate::stitchItem($relatedItem, ['media', 'links', 'categories'])
                ];
            }, $item->relationships());
        }

        return $object;
    }

    // 2c. pass through for processing multiple items
    public static function stitchItems($items)
    {
        $processed = $items->map(fn($item) => ItemGate::stitchItem($item));
        return $processed;
    }

    // QUERIES
    // 1. Index (only the bare minimum for everything, useful to generate url's in the frontend)
    public static function index()
    {
        $items = Item::get(['id', 'slug']);
        return $items;
    }

    // 2. Get all items, with optional parameters to filter by language or category id
    public static function all()
    {
        // Build query step by step
        $query = Item::with(ItemGate::getItemCompanions());

        // Add language filter
        // filter languages by name
        if (request()->query('languages')) {
            $query = $query->whereHas('language', function ($q) {
                $q->whereIn('short_name', request()->query('languages'));
            });
        }
        // or by id
        if (request()->query('language_ids')) {
            $query = $query->whereHas('language', function ($q) {
                $q->whereIn('id', request()->query('language_ids'));
            });
        }

        // Add category filter
        // filter categories by slug
        if (request()->query('categories')) {
            $query = $query->whereHas('categories', function ($q) {
                $q->whereIn('slug', request()->query('categories'));
            });
        }
        // or by id
        if (request()->query('category_ids')) {
            $query = $query->whereHas('categories', function ($q) {
                $q->whereIn('category_id', request()->query('category_ids'));
            });
        }

        // Determine order (descending or ascending)
        if (request()->query('order') == 'ascending') {
            $items = $query->orderBy('created_at', 'asc')->get();
        } else {
            $items = $query->orderBy('created_at', 'desc')->get();
        }

        // Wrap into object with meta data
        $response = [
            'count' => $items->count(),
            'result' => ItemGate::stitchItems($items)
        ];
        return $response;
    }

    // 3. Get single item
    public static function get($param)
    {
        // If parameter is an int, match it with an id
        if (is_int($param))
            $item = Item::where('id', $param)->with(ItemGate::getItemCompanions())->first();

        // If parameter is a string, match it with a slug
        if (is_string($param))
            $item = Item::where('slug', $param)->with(ItemGate::getItemCompanions())->first();

        // If the item is an update to another item, fetch the real one
        if ($item && $item->type != 'master') {
            $masterId = $item->parents->first()->id;
            $item = Item::where('id', $masterId)->with(ItemGate::getItemCompanions())->first();
        }

        return ItemGate::stitchItem($item);
    }
}
