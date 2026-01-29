<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageGate extends Controller
{
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
    public static function get($id)
    {
        $language = Language::where('id', $id)->with([
            'items:id,language_id',
        ])->first();
        $itemIds = $language->items->map(function (Item $item) {
            return $item->id;
        });
        $items = Item::whereIn('id', $itemIds)->with(ItemGate::$companions)->get();

        return $data = [
            'id' => $language->id,
            'name' => $language->name,
            'short' => $language->short_name,
            'native' => $language->native_name,
            'items' => ItemGate::composeItems($items),
        ];
        
    }
}
