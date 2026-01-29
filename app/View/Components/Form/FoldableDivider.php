<?php

namespace App\View\Components\Form;

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
        return view('components.form.foldable-divider');
    }
}
