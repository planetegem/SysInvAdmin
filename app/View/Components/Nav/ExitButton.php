<?php

namespace App\View\Components\Nav;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ExitButton extends Component
{
    public function __construct()
    {
    }

    public function render(): View|Closure|string
    {
        return view('components.nav.exit-button');
    }
}
