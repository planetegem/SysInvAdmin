<?php

namespace App\Http\Controllers;

use App\Models\Medium;
use App\Traits\ValidatesMedia;
use Illuminate\Http\Request;

class MediumController extends Controller
{
    use ValidatesMedia;

    // Fetch order list: used in all views
    private function fetchAll()
    {
        $orderDirection = isset($_GET['orderBy']) && $_GET['orderBy'] == 'name' ? 'asc' : 'desc';
        $orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'updated_at';

        return Medium::orderBy($orderBy, $orderDirection)->get();
    }

    // Single validation used for both create and update
    private function validateRequest($request)
    {
        // Validation rules
        $validatedData = $request->validate(
            $this->mediaRules('medium', false),
            $this->mediaMessages('medium')
        );

        // POST-VALIDATION
        // Media processing logic
        try {
            $processedMedia = Medium::processInput($request->medium);
            $validatedData['processed_media'] = $processedMedia;

            return $validatedData;

        } catch (\Exception $e) {

            // If image processing fails, add it to the errors and throw a formal ValidationException
            $validatedData->errors()->add('medium.type', $e->getMessage());
            throw new \Illuminate\Validation\ValidationException($validatedData);
        }
    }

    // Display a listing of the resource
    public function index()
    {
        $media = $this->fetchAll();
        return view('modules.media.index', compact('media'));
    }

    // Show the form for creating a new resource.
    public function create()
    {
        $media = $this->fetchAll();
        $medium = new Medium();
        return view('modules.media.create', compact('medium', 'media'));
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        // Validation
        $validated = $this->validateRequest($request);

        // Create the db entry and save the files
        $medium = Medium::create(['type' => $validated['medium']['type']]);
        $medium->resolveFiles($validated['processed_media']);

        // Return with succes message
        $message = __('media.module.message.created', ['name' => $medium->name]);
        return redirect()->route('media.index', $request->query())->with('succes', $message);
    }

    // Display (and edit) the specified resource.
    public function show(Medium $medium)
    {
        $media = $this->fetchAll();
        return view('modules.media.edit', compact('medium', 'media'));
    }

    // Show the form for editing the specified resource (unused because show does the same)
    public function edit(Medium $medium)
    {
        return $this->show($medium);
    }

    // Update the specified resource in storage.
    public function update(Request $request, Medium $medium)
    {
        // Validation
        $validated = $this->validateRequest($request);

        // Create the db entry and save the files
        $medium->update(['type' => $validated['medium']['type']]);
        $medium->resolveFiles($validated['processed_media']);

        // If medium needs to be detached, set mediable to null
        if ($request->detach_medium) {
            $medium->mediable_type = null;
            $medium->mediable_id = null;
            $medium->save();
        }

        // Return with succes message
        $message = __('media.module.message.updated', ['name' => $medium->name]);
        return redirect()->route('media.index', $request->query())->with('succes', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Medium $medium)
    {
        $message = __('media.module.message.deleted', ['name' => $medium->name]);
        $medium->delete();

        return redirect()->route('media.index', $request->query())->with('succes', $message);
    }
}
