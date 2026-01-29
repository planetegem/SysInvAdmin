<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;

use App\Models\Item;
use Illuminate\Http\Request;

class CategoryGate extends Controller
{
    public static function all()
    {
        $query = Category::with([
            'items:id',
        ])->where('hidden', '0')->orWhere('hidden', null);

        if(request()->query('includeHidden') == 'true'){
            $query->orWhere('hidden', '1');
        }
        $categories = $query->get();

        return $data = $categories->map(function (Category $category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'title' => $category->title,
                'description' => $category->description,
                'meta_title' => $category->meta_title,
                'meta_description' => $category->meta_description,
                'hidden' => $category->hidden,
                'items' => $category->items->map(function (Item $item) {
                    return $item->id;
                }),
            ];
        });
    }
    public static function get($id)
    {
        $category = Category::where('id', $id)->with([
            'items:id',
        ])->first();
        $itemIds = $category->items->map(function (Item $item) {
            return $item->id;
        });
        $items = Item::whereIn('id', $itemIds)->with(ItemGate::$companions)->get();

        return $data = [
            'id' => $category->id,
            'name' => $category->name,
            'title' => $category->title,
            'description' => $category->description,
            'meta_title' => $category->meta_title,
            'meta_description' => $category->meta_description,
            'items' => ItemGate::composeItems($items),
        ];
    }
}
