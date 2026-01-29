<?php

namespace App\View\Components\Form\Templates;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CreateForm extends Component
{
    public $controller;


    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.templates.create-form');
    }
}
