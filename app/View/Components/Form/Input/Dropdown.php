<?php

namespace App\View\Components\Form\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    public $id;
    public $label;
    public $tooltip;

    public function __construct($id, $label, $tooltip = false)
    {
        $this->id = $id;
        $this->label = $label;
        $this->tooltip = $tooltip;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.input.dropdown');
    }
}
