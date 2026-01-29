<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medium extends Model
{
    protected $fillable = ['file_type', 'file_name', 'file_path', 'alt', 'item_id'];

    public function item() {
        return $this->belongsTo(Item::class);
    }

}
