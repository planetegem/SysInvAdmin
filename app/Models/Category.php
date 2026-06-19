<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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



    public function items()
    {
        return $this->belongsToMany(Item::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $slug = Str::slug($model->name);
            $originalSlug = $slug;
            $count = 1;

            while (static::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            $model->slug = $slug;
        });
    }

    
    // STRINGIFIERS
    // Helper method to quickly get timestamps
    public function getTimestampsAsString()
    {
        return __(
            'category.properties.timestamps',
            ['created' => $this->created_at->format('d/m/Y'), 'updated' => $this->updated_at->format('d/m/Y'),]
        );
    }

}
