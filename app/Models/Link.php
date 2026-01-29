<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'anchor', 'url', 'item_id', 'order'
    ];
    public function Item(){
        return $this->belongsTo(Item::class);
    }

}
