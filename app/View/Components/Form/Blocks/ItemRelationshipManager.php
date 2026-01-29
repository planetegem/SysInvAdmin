<?php

namespace App\View\Components\Form\Blocks;

use App\Models\Item;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ItemRelationshipManager extends Component
{
    public $item;
    public $possibleRelations;

    public function __construct($item)
    {
        $this->item = $item;
        $this->possibleRelations = Item::where('type', 'master')->orderBy('title')->get();
    }

    public function render(): View|Closure|string
    {
        return view('components.form.blocks.item-relationship-manager');
    }
}
