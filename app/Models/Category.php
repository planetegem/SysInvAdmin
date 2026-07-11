<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasTimestamps;


class Category extends Model
{
    use HasTimestamps;

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
}
