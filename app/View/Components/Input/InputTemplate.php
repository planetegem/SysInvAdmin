<?php

namespace App\View\Components\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

abstract class InputTemplate extends Component
{
    protected string $componentPath;
    public $name;
    public $id;
    public $value;
    public $tooltip;
    public $label;

    public function __construct($value = "", $name = null, $id = null, $label = null, $tooltip = null)
    {
        $this->value = $value;
        $this->name = $name ?? $id;
        $this->id = $id ?? $name;
        $this->label = $label;
        $this->tooltip = $tooltip;
    }

    public function render(): View|Closure|string
    {
        return view($this->componentPath);
    }
}
