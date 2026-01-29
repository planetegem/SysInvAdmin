<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'title',
        'description',
        'meta_title',
        'meta_description',
        'hidden'
    ];

    public function formattedTimestamps(){
        return "Created on {$this->created_at->format('d/m/Y')} | Last updated on {$this->updated_at->format('d/m/Y')}";
    }
    public function confirmDelete(){
        return
            "Are you sure you want to delete category #{$this->id} ({$this->name})?
            <br>
            This category is currently attached to {$this->items()->count()} items.";
    }

    public function details(){
        return 
            "Category #{$this->id} ({$this->name})
            <br>
            Attached to {$this->items()->count()} items | {$this->formattedTimestamps()}";
    }

    public function items(){
        return $this->belongsToMany(Item::class);
    }



}
