<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Language;
use Illuminate\Http\Request;

use OpenApi\Attributes as OA;


class LanguageGate extends Controller
{
    // RESPONSE HELPERS
    // 1. OpenAPI schema objects
    #[OA\Schema(
        schema: "Language",
        title: "Language",
        description: "A Language that an item might be expressed in",
        type: "object",
        properties: [
            new OA\Property(property: 'id', type: 'int', example: 1),
            new OA\Property(property: 'name', type: 'string', example: 'French'),
            new OA\Property(property: 'short_name', type: 'string', example: 'fr'),
            new OA\Property(property: 'native_name', type: 'string', example: 'français'),
            new OA\Property(property: 'items', type: 'array', items: new OA\Items(ref: "#/components/schemas/Item")),
        ]
    )]
    
    // QUERIES
    // 1. Fetch all languages
    public static function all()
    {
        $languages = Language::with([
            'items:id,language_id',
        ])->get();

        return $data = $languages->map(function (Language $language) {
            return [
                'name' => $language->name,
                'short' => $language->short_name,
                'native' => $language->native_name,
                'items' => $language->items->map(function (Item $item) {
                    return $item->id;
                }),
            ];
        });

    }

    // 2. Fetch specific language with all dependant items
    public static function get($param)
    {
        if (is_int($param))
            $language = Language::where('id', $param)->with([
                'items:id,language_id',
            ])->first();

        if (is_string($param))
            $language = Language::where('short_name', $param)->with([
                'items:id,language_id',
            ])->first();
        
        $itemIds = $language->items->map(function (Item $item) {
            return $item->id;
        });
        $item_request = Item::whereIn('id', $itemIds)->with(ItemGate::getItemCompanions(['categories', 'links', 'media']));

        // Determine order (descending or ascending)
        switch (request()->query('order')) {
            case 'creation-ascending':
                $items = $item_request->orderBy('created_at', 'asc')->get();
                break;
            case 'creation-descending':
            default:
                $items = $item_request->orderBy('created_at', 'desc')->get();
                break;
        }

        return $data = [
            'id' => $language->id,
            'name' => $language->name,
            'short' => $language->short_name,
            'native' => $language->native_name,
            'items' => ItemGate::stitchItems($items),
        ];
        
    }
}
