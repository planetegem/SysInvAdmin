<?php

namespace App\View\Components\Input\Blocks;

use App\Models\Item;
use App\Models\Relationship;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RelationshipManager extends Component
{
    // Id to used in construction of input element names
    public $id;

    // Options in select elements
    public $possibleRelationships;
    public $possibleTargets;

    // Relevant info for selected item
    public $item;

    public function __construct($id, $item)
    {
        $this->id = $id;
        $this->item = $item;

        $this->possibleTargets = Item::where('id', '!=', $item->id)->get();

        // Prepare relationships
        $possibleRelationships = Relationship::all()->map(function ($rel) {
            $result = [];
            $result[] = [
                'label' => $rel->subject_descriptor,
                'value' => $rel->name . '|' . $rel->subject_label,
            ];
            if ($rel->type != 'lateral')
                $result[] = [
                    'label' => $rel->object_descriptor,
                    'value' => $rel->name . '|' . $rel->object_label,
                ];
            return $result;
        });
        

        $this->possibleRelationships = array_merge(...$possibleRelationships->toArray());
    }

    public function render(): View|Closure|string
    {
        return view('components.input.blocks.relationship-manager');
    }
}
