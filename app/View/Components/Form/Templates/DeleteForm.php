<?php

namespace App\View\Components\Form\Templates;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DeleteForm extends Component
{
    public $controller;
    public $selected;

    public function __construct($controller, $selected)
    {
        $this->controller = $controller;
        $this->selected = $selected;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.templates.delete-form');
    }
}
