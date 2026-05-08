<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    // BASE PROPS & METHODS   
    // Fillable properties (update allowed after creation)
    protected $fillable = [
        'content',
        'type',
        'blockable_id',
        'blockable_type',
        'updated_at',
        'file_type'
    ];
}
