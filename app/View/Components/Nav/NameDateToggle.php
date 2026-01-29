<?php

namespace App\View\Components\Nav;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NameDateToggle extends Component
{
    public $nameClass = '';
    public $updateClass = '';
    public function __construct()
    {
        if (isset($_GET['orderBy']) && $_GET['orderBy'] == 'name'){
            $this->nameClass = 'class=selected';
        } else {
            $this->updateClass = 'class=selected';
        }
    }
    
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.nav.name-date-toggle');
    }
}
