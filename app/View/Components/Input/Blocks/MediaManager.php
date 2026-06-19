<?php

namespace App\View\Components\Input\Blocks;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MediaManager extends Component
{
    public $item;
    public $id;
    public function __construct($item, $id)
    {
        $this->item = $item;
        $this->id = $id;
    }

    public function render(): View|Closure|string
    {
        return view('components.input.blocks.media-manager');
    }
}
