<?php

namespace App\View\Components\Input\Blocks;

use App\Models\Category;
use App\View\Components\Input\InputTemplate;
use Closure;
use Illuminate\Contracts\View\View;

class CategoryManager extends InputTemplate
{
    public $allCategories;
    public $selectedCategories;
    public $prefix;

    public function __construct($selected, $tooltip = null, $label = null, $type = 'normal')
    {
        parent::__construct("", "category_manager", null, $label, $tooltip);

        if ($type == 'normal') {
            $this->allCategories = Category::where(function ($query){
                $query->where('hidden', '0')->orWhere('hidden', null);
            })->get();
            $this->prefix = '';
        } else if ($type == 'hidden'){
            $this->allCategories = Category::where('hidden', '1')->get();
            $this->prefix = 'hidden_';
        }
        $this->selectedCategories = $selected;

    }

    public function render(): View|Closure|string
    {
        return view('components.input.blocks.category-manager');
    }
}
