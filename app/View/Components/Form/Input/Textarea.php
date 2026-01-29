<?php

namespace App\View\Components\Form\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    public $name;
    public $value;
    public $optional;

    public function __construct($name, $value, $optional = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->optional = $optional;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.input.textarea');
    }
}
