<?php

namespace App\View\Components\Nav;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;


class IndexItem extends Component
{

    public $name;
    public $target;
    public $timestamp;
    public $icon;
    public $class;

    
    public function __construct($name, $target, $timestamp, $icon = "styles/icons/description_icon.svg")
    {
        $this->name = $name;
        $this->target = $target;
        $this->icon = $icon;

        // SELECTION LOGIC
        $this->class = (url()->current() == $target) ? "selected" : "not-selected";

        // DATE LOGIC
        $last_update = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp);

        if( $last_update->isToday()) 
        {
            $secondsAgo = (Carbon::now()->getTimestamp() - $last_update->timestamp);

            if ($secondsAgo >= 3600)
            {
                $this->timestamp = 'updated ' . floor($secondsAgo / 3600) . ' hours ago';
            } 
            else if ($secondsAgo >= 60)
            {
                $this->timestamp = 'updated ' . floor($secondsAgo / 60) . ' minutes ago';
            } 
            else
            {
                $this->timestamp = 'updated ' . $secondsAgo . ' seconds ago';
            }
           
        } 
        else if ($last_update->isYesterday())
        {
            $this->timestamp = 'updated yesterday';
        } 
        else
        {
            $this->timestamp = 'updated ' . $last_update->format('d/m/y');
        }
    }


    public function render(): View|Closure|string
    {
        return view('components.nav.index-item');
    }
}
