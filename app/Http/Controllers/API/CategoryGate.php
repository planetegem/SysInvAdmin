<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use OpenApi\Attributes as OA;


class CategoryGate extends Controller
{
    // RESPONSE HELPERS
    // 1. OpenAPI schema objects
    #[OA\Schema(
        schema: "Category",
        title: "Category",
        description: "A category used to label or tag items",
        type: "object",
        properties: [
            new OA\Property(property: 'id', type: 'int', example: 11),
            new OA\Property(property: 'name', type: 'string', example: 'Het Vleeskanon'),
            new OA\Property(property: 'slug', type: 'string', example: 'het-vleeskanon'),
            new OA\Property(property: 'title', type: 'string', example: 'The Meatcannon (all chapters)'),
            new OA\Property(property: 'description', type: 'string', example: 'A short (or absent) description of the category, for public use'),
            new OA\Property(property: 'meta_title', type: 'string', example: 'SEO title of the category'),
            new OA\Property(property: 'meta_description', type: 'string', example: 'SEO description of the category'),
            new OA\Property(property: 'hidden', type: 'boolean', example: true),
            new OA\Property(property: 'items', type: 'array', items: new OA\Items(ref: "#/components/schemas/Item")),
        ]
    )]

    // QUERIES
    // 1. Index (only the bare minimum for everything, useful to generate url's in the frontend)
    public static function index()
    {
        $items = Category::get(['id', 'slug']);
        return $items;
    }

    // 2. Get all categories
    public static function all()
    {
        $query = Category::with([
            'items:id',
        ])->where('hidden', '0')->orWhere('hidden', null);

        if (request()->query('includeHidden') == 'true') {
            $query->orWhere('hidden', '1');
        }
        $categories = $query->get();

        return $data = $categories->map(function (Category $category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'title' => $category->title,
                'description' => $category->description,
                'meta_title' => $category->meta_title,
                'meta_description' => $category->meta_description,
                'hidden' => $category->hidden == 0 ? false : true,
                'items' => $category->items->map(function (Item $item) {
                    return $item->id;
                }),
            ];
        });
    }

    // 3. Get single category with all dependant items
    public static function get($param)
    {
        if (is_int($param))
            $category = Category::where('id', $param)->with([
                'items:id',
            ])->first();

        if (is_string($param))
            $category = Category::where('slug', $param)->with([
                'items:id',
            ])->first();

        $itemIds = $category->items->map(function (Item $item) {
            return $item->id;
        });
        $items = Item::whereIn('id', $itemIds)->with(ItemGate::getItemCompanions())->get();

        return $data = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'title' => $category->title,
            'description' => $category->description,
            'meta_title' => $category->meta_title,
            'meta_description' => $category->meta_description,
            'hidden' => $category->hidden == 0 ? false : true,
            'items' => ItemGate::stitchItems($items),
        ];
    }
}
