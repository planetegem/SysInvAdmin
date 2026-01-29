<?php

namespace App\View\Components\Form\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Checkbox extends Component
{
    public $name;
    public $checked;
    public function __construct($name, $checked = false)
    {
        $this->name = $name;
        $this->checked = ($checked && $checked > 0);
    }

    public function render(): View|Closure|string
    {
        return view('components.form.input.checkbox');
    }
}
