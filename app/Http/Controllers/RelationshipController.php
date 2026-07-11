<?php

namespace App\Http\Controllers;

use App\Models\Relationship;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RelationshipController extends Controller
{

    // Fetch order list: used in all views

    private function fetchAll()
    {
        $orderDirection = isset($_GET['orderBy']) && $_GET['orderBy'] == 'name' ? 'asc' : 'desc';
        $orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'updated_at';

        return Relationship::orderBy($orderBy, $orderDirection)->get();
    }

    // Single validation used for both create and update
    private function validateRequest(Request $request)
    {
        // In case of update: fetch id being updated
        $relationship = $request->route('relationship');
        $relationshipId = is_object($relationship) ? $relationship->id : $relationship;

        // Validation rules
        $validated = $request->validate([
            'relationship_name' => [
                'required',
                'string',
                Rule::unique('relationships', 'name')->ignore($relationshipId)
            ],
            'relationship_type' => 'required|string',
            'relationship_subject_label' => 'required|string',
            'relationship_object_label' => 'required_unless:relationship_type,lateral|string',
            'relationship_subject_descriptor' => 'required|string',
            'relationship_object_descriptor' => 'required_unless:relationship_type,lateral|string'
        ]);

        // If horizontal relationship, fill object_label & object_descriptor
        if ($validated['relationship_type'] === 'lateral') {
            $validated['relationship_object_label'] = $validated['relationship_subject_label'];
            $validated['relationship_object_descriptor'] = $validated['relationship_subject_descriptor'];
        }

        return $validated;
    }

    // Display a listing of the resource.
    public function index()
    {
        $relationships = $this->fetchAll();
        return view('modules.relationships.index', compact('relationships'));
    }

    // Show the form for creating a new resource.
    public function create()
    {
        $relationships = $this->fetchAll();
        $relationship = new Relationship();
        return view('modules.relationships.create', compact('relationship', 'relationships'));
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {

        $validated = $this->validateRequest($request);

        $relationship = Relationship::create([
            'name' => $validated['relationship_name'],
            'type' => $validated['relationship_type'],
            'subject_label' => $validated['relationship_subject_label'],
            'object_label' => $validated['relationship_object_label'],
            'subject_descriptor' => $validated['relationship_subject_descriptor'],
            'object_descriptor' => $validated['relationship_object_descriptor']

        ]);

        $message = __('relationship.message.create_ok', ["id" => $relationship->id, "name" => $relationship->name]);

        return redirect()->route('relationships.index', $request->query())->with('succes', $message);
    }

    // Display the specified resource.
    public function show(Relationship $relationship)
    {
        $relationships = $this->fetchAll();
        return view('modules.relationships.edit', compact('relationship', 'relationships'));
    }

    // Show the form for editing the specified resource.
    // NOT USED
    public function edit(Relationship $relationship)
    {
    }

    // Update the specified resource in storage.
    public function update(Request $request, Relationship $relationship)
    {
        $validated = $this->validateRequest($request);

        $relationship->update([
            'name' => $validated['relationship_name'],
            'type' => $validated['relationship_type'],
            'subject_label' => $validated['relationship_subject_label'],
            'object_label' => $validated['relationship_object_label'],
            'subject_descriptor' => $validated['relationship_subject_descriptor'],
            'object_descriptor' => $validated['relationship_object_descriptor']

        ]);

        $message = __('relationship.message.update_ok', ["id" => $relationship->id, "name" => $relationship->name]);

        return redirect()->route('relationships.index', $request->query())->with('succes', $message);
    }


    // Remove the specified resource from storage.
    public function destroy(Request $request, Relationship $relationship)
    {
        $message = __('relationship.message.delete_ok', ["id" => $relationship->id, "name" => $relationship->name]);
        $relationship->delete();

        return redirect()->route('relationships.index', $request->query())->with('succes', $message);
    }
}
