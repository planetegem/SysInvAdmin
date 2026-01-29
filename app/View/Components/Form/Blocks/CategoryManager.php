<?php

namespace App\View\Components\Form\Blocks;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CategoryManager extends Component
{
    public $allCategories;
    public $selectedCategories;
    public $prefix;

    public function __construct($selected, $type = 'normal')
    {
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
        return view('components.form.blocks.category-manager');
    }
}
