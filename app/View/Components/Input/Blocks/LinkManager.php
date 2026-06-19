<?php

namespace App\View\Components\Input\Blocks;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LinkManager extends Component
{
    public $links;
    public function __construct($links)
    {
        $this->links = $links;
    }

    public function render(): View|Closure|string
    {
        return view('components.input.blocks.link-manager');
    }
}
