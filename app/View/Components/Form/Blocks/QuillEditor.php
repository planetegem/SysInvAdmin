<?php

namespace App\View\Components\Form\Blocks;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class QuillEditor extends Component
{
    public $name;
    public $id;
    public $content_type;
    public $content_body;
    public $optional;

    public function __construct($name, $id, $content, $optional = false)
    {
        $this->name = $name;
        $this->id = $id;
        $this->content_type = $content->type ?? '';
        $this->content_body = $content->content ?? '';
        $this->optional = $optional;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.blocks.quill-editor');
    }
}
