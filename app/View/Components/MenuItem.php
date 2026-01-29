<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class MenuItem extends Component
{

    public $target;
    public $icon;
    public $class = 'not-selected';
    /**
     * Create a new component instance.
     */
    public function __construct($target, $icon)
    {
        $this->target = $target;
        $this->icon = $icon;

        if (Route::currentRouteName() == "{$target}.index" || Route::currentRouteName() == "{$target}.create" || Route::currentRouteName() == "{$target}.show"){
            $this->class = 'selected';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.menu-item');
    }
}
