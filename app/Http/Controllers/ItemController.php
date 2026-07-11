<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Language;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    // Fetch order list: used in all views
    private function fetchAll()
    {
        $orderDirection = isset($_GET['orderBy']) && $_GET['orderBy'] == 'name' ? 'asc' : 'desc';
        $orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'updated_at';

        return Item::orderBy($orderBy, $orderDirection)->get();
    }

    // Single validation used for both create and update
    private function validateRequest($request)
    {
        // PRE-VALIDATION
        // In case of update: fetch id being updated
        $item = $request->route('item');
        $itemId = is_object($item) ? $item->id : $item;

        // Clean meaningless relationships
        $relationships = collect($request->input('item_relationships', []))
            ->reject(fn($row) => empty($row['type']) | empty($row['item']))
            ->toArray();
        $request->merge(['item_relationships' => $relationships]);

        // VALIDATION PHASE
        // 1. Base validation
        $validated = Validator::make(
            $request->all(),
            [
                'item_title' => [
                    'required',
                    'string',
                    Rule::unique('items', 'title')->ignore($itemId)
                ],
                'item_description.content' => ['required'],
                'item_description.type' => ['required'],
                'language_dropdown' => ['required'],

                // Media validation
                'item_media.type' => ['required', 'in:none,image,carousel,image-list'],
                'item_media.path' => [
                    'required_if:item_media.type,image,carousel,image-list',
                    'array'
                ],
                'item_media.path.*' => ['required', 'string'],

                // Links validation
                'item_links.*.anchor' => ['nullable', 'required_with:item_links.*.url'],
                'item_links.*.url' => ['nullable', 'required_with:item_links.*.anchor'],

                // Relationships validation
                'item_relationships' => 'nullable|array',
                'item_relationships.*.type' => 'required_with:item_relationships.*.item|string',
                'item_relationships.*.item' => 'required_with:item_relationships.*.type|integer|exists:items,id',
            ],
            [
                // CUSTOM MESSAGES
                // Links
                'item_links.*.anchor.required_with' => __('custom_validation.item.link.missing_anchor'),
                'item_links.*.url.required_with' => __('custom_validation.item.link.missing_url'),

                // Description
                'item_description.content.required' => __('custom_validation.item.description_missing'),
                'item_description.type.required' => __('custom_validation.item.description_missing'),

                // Relationships
                'item_relationships.*.type.required_with' => __('custom_validation.item.relationship.missing_type'),
                'item_relationships.*.item' => __('custom_validation.item.relationship.missing_target'),

                // Media
                'item_media.path.required_if' => __('custom_validation.media.path_missing'),
                'item_media.type.in' => __('custom_validation.media.not_supported'),
            ]
        );

        // 2. Extra validation step for relationships: can only establish one type of relationship to each item
        $validated->after(function ($validated) use ($request) {
            $relationships = $request->input('item_relationships', []);
            $itemIds = array_column($relationships, 'item');

            foreach ($itemIds as $index => $itemId) {
                $matchingKeys = array_keys($itemIds, $itemId);

                if (count($matchingKeys) > 1 && $matchingKeys[0] !== $index) {
                    $validated->errors()->add(
                        "item_relationships.{$index}.item",
                        "You have already established a relationship with item ID {$itemId}."
                    );
                }
            }
        });

        // RETURN ON VALIDATION FAIL
        $validatedData = $validated->validated();

        // POST-VALIDATION
        // Image processing logic
        try {
            $processedMedia = Item::processMedia($request->item_media);
            $validatedData['processed_media'] = $processedMedia;

            return $validatedData;

        } catch (\Exception $e) {

            // If image processing fails, add it to the errors and throw a formal ValidationException
            $validated->errors()->add('item_media.type', $e->getMessage());
            throw new \Illuminate\Validation\ValidationException($validated);
        }
    }

    // Process item relationships
    private function setRelationships(array $validated, Item $item)
    {
        // 1. Clear all established relations

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
        // $item->setRelationships($request->relationship);

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

        // 4. Item description
        $item->contentBlocks()->delete();
        $item->contentBlocks()->create([
            'type' => $request->input('item_description.type'),
            'content' => $request->input('item_description.content')
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $items = $this->fetchAll();
        return view('modules.items.index', compact('items'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = $this->fetchAll();
        $item = new Item();
        return view('modules.items.create', compact('item', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        // DB UPDATES
        // 1. Basic props: title and description
        $language = Language::where('id', intval($request->language_dropdown))->first();
        $item = $language->items()->create([
            'title' => $validated['item_title'],
        ]);

        // 2. Dependencies
        $this->storeAndUpdate($request, $item);

        // 3. Add file to media table (if updating, first delete files)
        $item->saveMedia($validated['processed_media']);

        // REFACTORED: loading dependencies
        $item->setRelationships($validated['item_relationships']);

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
        return view('modules.items.edit', compact('item', 'items'));
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
        $validated = $this->validateRequest($request);

        // DB UPDATES
        // 1. Basic props: title and description
        $item->update([
            'title' => $request->item_title,
            'language_id' => intval($request->language_dropdown),
        ]);
        $item->touch();

        // 2. Dependencies
        $this->storeAndUpdate($request, $item);

        // 3. Add file to media table (if updating, first delete files)
        $item->saveMedia($validated['processed_media']);

        // REFACTORED: loading dependencies
        $item->setRelationships($validated['item_relationships']);

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

        if ($item->hasChildren()) {
            foreach ($item->children as $child) {
                $child->update(['type' => 'master']);
            }
        }
        $item->delete();

        return redirect()->route('items.index', $request->query())->with('succes', $message);
    }
}
