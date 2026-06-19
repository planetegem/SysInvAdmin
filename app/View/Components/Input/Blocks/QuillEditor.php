<?php

namespace App\View\Components\Input\Blocks;

use App\View\Components\Input\InputTemplate;
use Closure;
use Illuminate\Contracts\View\View;

class QuillEditor extends InputTemplate
{

    public $content_type;
    public $content_body;

    public function __construct($name, $id, $content, $tooltip = null, $label = null)
    {
        parent::__construct("", $name, $id, $label, $tooltip);
        $this->content_type = $content->type ?? '';
        $this->content_body = $content->content ?? '';
    }

    public function render(): View|Closure|string
    {
        return view('components.input.blocks.quill-editor');
    }
}
