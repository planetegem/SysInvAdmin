<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    public function items(){
        return $this->hasMany(Item::class);
    }
}
