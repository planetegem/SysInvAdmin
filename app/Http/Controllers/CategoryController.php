<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    /**
     * Fetch order list: used in all views
     */
    private function fetchAll()
    {
        $orderDirection = isset($_GET['orderBy']) && $_GET['orderBy'] == 'name' ? 'asc' : 'desc';
        $orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'updated_at';

        return Category::orderBy($orderBy, $orderDirection)->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = $this->fetchAll();
        return view('inventory.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->fetchAll();
        $category = new Category();
        return view('inventory.categories.create', compact('category', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => [
                'required',
                'unique:categories,name'
            ]
        ]);
        $category = Category::create([
            'name' => $request->category_name,
            'title' => $request->category_title,
            'description' => $request->category_description,
            'meta_title' => $request->category_meta_title,
            'meta_description' => $request->category_meta_description,
            'hidden' => $request->hidden_category ? true : false
        ]);

        $message = "Category #{$category->id} ({$category->name}) has been succesfully created.";

        return redirect()->route('categories.index', $request->query())->with('succes', $message);
    }

    /**
     * Display (and edit) the specified resource.
     */
    public function show(Category $category)
    {
        $categories = $this->fetchAll();
        return view('inventory.categories.edit', compact('category', 'categories'));
    }

    /**
     * Show the form for editing the specified resource -> not used because of show does the same thing
     */
    public function edit(Category $category)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name' => [
                'required',
                'unique:categories,name,' . $category->id,
            ]
        ]);

        $category->update([
            'name' => $validated['category_name'],
            'title' => $request->category_title,
            'description' => $request->category_description,
            'meta_title' => $request->category_meta_title,
            'meta_description' => $request->category_meta_description,
            'hidden' => $request->hidden_category ? true : false
        ]);

        $message = "Category #{$category->id} ({$category->name}) has been succesfully updated.";

        return redirect()->route('categories.index', $request->query())->with('succes', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Category $category)
    {
        $message = "Category #{$category->id} ({$category->name}) has been succesfully deleted.";

        $category->delete();
        return redirect()->route('categories.index', $request->query())->with('succes', $message);
    }
}
