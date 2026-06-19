<?php

namespace App\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FoldableDivider extends Component
{
    public $title;
    public function __construct($title)
    {
        $this->title = $title;
    }

    public function render(): View|Closure|string
    {
        return view('components.layout.foldable-divider');
    }
}
