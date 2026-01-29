<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PoppableBalloon extends Component
{
    public $value;
    public $name;
    public $checked;
    
    public function __construct($value, $name, $checked = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->checked = $checked;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.poppable-balloon');
    }
}
