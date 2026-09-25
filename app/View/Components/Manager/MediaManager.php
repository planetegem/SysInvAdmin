<?php

namespace App\View\Components\Manager;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MediaManager extends Component
{
    public $medium;
    public $id;
    public function __construct($medium, $id)
    {
        $this->medium = $medium;
        $this->id = $id;
    }

    public function render(): View|Closure|string
    {
        return view('components.manager.media-manager');
    }
}
