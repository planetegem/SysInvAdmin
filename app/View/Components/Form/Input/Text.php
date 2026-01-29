<?php

namespace App\View\Components\Form\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Text extends Component
{
    public $name;
    public $value;
    public $optional;
    public $tooltip;
    public $label;

    public function __construct($name, $value, $optional = false, $tooltip = true, $label = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->optional = $optional;
        $this->tooltip = $tooltip;
        $this->label = $label ?? $name;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.input.text');
    }
}
