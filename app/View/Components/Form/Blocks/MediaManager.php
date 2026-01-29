<?php

namespace App\View\Components\Form\Blocks;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MediaManager extends Component
{
    public $item;
    public function __construct($item)
    {
        $this->item = $item;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.blocks.media-manager');
    }
}
