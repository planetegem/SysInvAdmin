<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

abstract class FormTemplate extends Component
{
    protected $componentPath;

    public $confirmRoute;
    public $cancelRoute;
    public $name;

    public function __construct($confirmRoute, $name = null, $cancelRoute = null)
    {
        $this->confirmRoute = $confirmRoute;
        $this->cancelRoute = $cancelRoute;
        $this->name = $name ?? $header ?? $confirmRoute;
    }

    public function render(): View|Closure|string
    {
        return view($this->componentPath);
    }
}
