<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function getItems()
    {
        return response()->json(ItemGate::all());
    }

    public function getItem($id)
    {
        return response()->json(ItemGate::get($id));
    }

    public function getCategories()
    {
        return response()->json(CategoryGate::all());
    }
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
