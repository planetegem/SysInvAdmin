<?php

namespace App\View\Components\Form\Input;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ListSelector extends Component
{
    public $label;
    public $id;
    public $list;
    public $callback;

    public function __construct($label, $id, $list, $callback)
    {
        $this->label = $label;
        $this->id = $id;
        $this->list = $list;
        $this->callback = $callback;
    }


    public function render(): View|Closure|string
    {
        return view('components.form.input.list-selector');
    }
}
