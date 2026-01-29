<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Link;
use Illuminate\Http\Request;

class ItemGate extends Controller
{
    private static function composeMedia(Item $item)
    {
        if (!$item->file_type)
            return null;

        $media = [
            'type' => $item->file_type,
            'files' => [],
        ];
        switch ($item->file_type) {
            case 'image':
            case 'thumbnail':
                foreach ($item->media as $medium) {
                    $media['files'][] = [
                        $medium->file_path,
                    ];
                    $media['alt'] = $medium->alt;
                }
                break;
            default:
                break;
        }
        return $media;
    }
    public static function composeItem($item, $fulltree = false)
    {
        $response = [
            'id' => $item->id,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
            'title' => $item->title,
            'description' => preg_replace('/\s+/', ' ', $item->description),
            'language' => $item->language,
            'media' => ItemGate::composeMedia($item), 
        ];

        if ($item->visibleCategories->count() > 0) 
            $response['categories'] = $item->visibleCategories->map(function (Category $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name
                ];
            });
        if ($item->links->count() > 0)
            $response['links'] = $item->links->map(function (Link $link) {
                return [
                    'anchor' => $link->anchor,
                    'url' => $link->url
                ];
            });
        
        if ($fulltree) {
            $response['relationships'] = array_map(function($relationship){
                return [
                    'relationship' => $relationship['relationship'],
                    'item' => ItemGate::composeItem(Item::where('id', $relationship['item'])->with(ItemGate::$companions)->first())
                ];
            }, $item->relationships());
        } else {
            $response['relationships'] = $item->relationships();
        }      

        return $response;
    }

    public static function composeItems($items)
    {
        
        $processed = $items->map(fn($item) => ItemGate::composeItem($item));
        return $processed;
    }

    public static $companions = [
        'categories:id,name',
        'links:id,item_id,anchor,url',
        'media:id,item_id,file_name,file_path,alt',
        'language:id,name'
    ];

    public static function all()
    {
        $query = Item::with(ItemGate::$companions);
        if (request()->query('languages')) {
            $query = $query->whereHas('language', function ($q) {
                $q->whereIn('id', request()->query('languages'));
            });
        }
        if (request()->query('namedCategory') == 'true') {
            // TO COMPLETE
        } else {
            if (request()->query('categories')) {
                $query = $query->whereHas('categories', function ($q) {
                    $q->whereIn('category_id', request()->query('categories'));
                });
            }
        }

        $items = $query->get();
        return ItemGate::composeItems($items);
    }

    public static function get($id){
        $item = Item::where('id', $id)->with(ItemGate::$companions)->first();

        // If the item is an update to another item, fetch the real one
        if ($item->type != 'master'){
            $masterId = $item->parents->first()->id;
            $item = Item::where('id', $masterId)->with(ItemGate::$companions)->first();
        }

        return ItemGate::composeItem($item, true);
    }
}
