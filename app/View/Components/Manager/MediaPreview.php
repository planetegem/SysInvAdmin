<?php

namespace App\View\Components\Manager;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MediaPreview extends Component
{

    public function __construct(
        public string $type
    ) {}
    
    public function render(): View|Closure|string
    {
        return view('components.manager.media-preview');
    }
}
