<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Language;

use App\Traits\SavesMedia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;


class ItemController extends Controller
{
    use SavesMedia;

    /**
     * Fetch order list: used in all views
     */
    private function fetchAll()
    {
        $orderDirection = isset($_GET['orderBy']) && $_GET['orderBy'] == 'name' ? 'asc' : 'desc';
        $orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'updated_at';

        return Item::orderBy($orderBy, $orderDirection)->get();
    }

    private function storeAndUpdate(Request $request, Item $item)
    {
        // 1. Categories
        $categories = [
            'visible' => $request->categories ?? [],
            'hidden' => $request->hidden_categories ?? [],
        ];
        $item->syncCategories($categories);

        // 2. Item relationships
        $item->setRelationships($request->relationship);

        // 3. Item links
        $item->links()->delete();
        foreach ($request->item_links as $index => $link) {
            if ($link['anchor'] && $link['url']) {
                $item->links()->create([
                    'anchor' => $link['anchor'],
                    'url' => $link['url'],
                    'order' => $index,
                ]);
            }
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $items = $this->fetchAll();
        return view('inventory.items.index', compact('items'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = $this->fetchAll();
        $item = new Item();
        return view('inventory.items.create', compact('item', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // VALIDATION PHASE
        // 1. Base validation
        $validator = Validator::make($request->all(), [
            'item_title' => [
                'required',
                'unique:items,title'
            ],
            'item_description' => ['required'],
            'language_dropdown' => ['required'],
            'file_type_dropdown' => ['required'],
            'item_links.*.anchor' => ['nullable', 'required_with:item_links.*.url'],
            'item_links.*.url' => ['nullable', 'required_with:item_links.*.anchor']
        ], [
            'item_links.*.anchor.required_with' => 'An anchor text is required when supplying a link URL.',
            'item_links.*.url.required_with' => 'An URL is required when supplying a link anchor.',
        ]);

        if ($validator->fails()) {
            return redirect('items/create')
                ->withErrors($validator)
                ->withInput();
        }

        // 2. Validate media
        $image_validator = $this->validateMedia($request);
        if ($image_validator['head'] == 'error') 
            return Redirect::back()->withErrors($image_validator['body']);

        // DB UPDATES
        // 1. Basic props: title and description
        $language = Language::where('id', intval($request->language_dropdown))->first();
        $item = $language->items()->create([
            'title' => $request->item_title,
            'description' => $request->item_description,
            'type' => 'master'
        ]);

        // 2. Dependencies
        $this->storeAndUpdate($request, $item);

        // 3. Add file to media table (if updating, first delete files)
        $this->saveMedia($image_validator, $item);

        // ALL DONE
        $message = "Item #{$item->id} ({$item->title}) has been succesfully created.";
        return redirect()->route('items.index', $request->query())->with('succes', $message);
    }

    /**
     * Display (and edit) the specified resource.
     */
    public function show(Item $item)
    {
        $items = $this->fetchAll();
        return view('inventory.items.edit', compact('item', 'items'));
    }

    /**
     * Show the form for editing the specified resource -> not used because of show does the same thing
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        // VALIDATION PHASE
        // 1. Base validation
        $validator = Validator::make($request->all(), [
            'item_title' => [
                'required',
                'unique:items,title,' . $item->id,
            ],
            'item_description' => ['required'],
            'language_dropdown' => ['required'],
            'file_type_dropdown' => ['required'],
            'item_links.*.anchor' => ['nullable', 'required_with:item_links.*.url'],
            'item_links.*.url' => ['nullable', 'required_with:item_links.*.anchor']
        ], [
            'item_links.*.anchor.required_with' => 'An anchor text is required when supplying a link URL.',
            'item_links.*.url.required_with' => 'An URL is required when supplying a link anchor.',
        ]);

        if ($validator->fails()) {
            return redirect('items/create')
                ->withErrors($validator)
                ->withInput();
        }

        // 1.1. Refuse to make update if already has updates itself
        if (isset($request->relationship['type']) && $request->relationship['type'] != 'nothing' && $item->hasChildren()){
            return Redirect::back()->withErrors(['relationship[type]' => "Cannot become a depency while already having dependencies."]);
        }
        
        // 2. Validate media
        $image_validator = $this->validateMedia($request, $item);
        if ($image_validator['head'] == 'error') 
            return Redirect::back()->withErrors($image_validator['body']);

        // DB UPDATES
        // 1. Basic props: title and description
        $item->update([
            'title' => $request->item_title,
            'description' => $request->item_description,
            'type' => 'master',
            'language_id' => intval($request->language_dropdown),
        ]);
        $item->touch();

        // 2. Dependencies
        $this->storeAndUpdate($request, $item);

        // 3. Add file to media table (if updating, first delete files)
        $this->saveMedia($image_validator, $item);

        // ALL DONE
        $message = "Item #{$item->id} ({$item->title}) has been succesfully updated.";
        return redirect()->route('items.index', $request->query())->with('succes', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Item $item)
    {
        $message = "Item #{$item->id} ({$item->title}) has been succesfully deleted.";

        if ($item->hasChildren()){
            foreach($item->children as $child) {
                $child->update(['type' => 'master']);
            }
        }
        $item->delete();

        return redirect()->route('items.index', $request->query())->with('succes', $message);
    }
}
