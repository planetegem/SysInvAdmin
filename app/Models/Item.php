<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    protected $fillable = [
        'title',
        'description',
        'language',
        'type',
        'language_id',
        'updated_at',
        'file_type'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    public function visibleCategories()
    {
        return $this->belongsToMany(Category::class)->where('hidden', '0');
    }
    public function syncCategories($categories)
    {

        $categories = array_merge(
            array_map(function ($cat) {
                return ['name' => $cat, 'hidden' => '0'];
            }, $categories['visible']),
            array_map(function ($cat) {
                return ['name' => $cat, 'hidden' => '1'];
            }, $categories['hidden'])
        );

        $category_ids = [];
        foreach ($categories as $category) {
            $cat = Category::firstOrCreate(['name' => $category['name']], ['hidden' => $category['hidden']]);
            $id = $cat->id;
            array_push($category_ids, $id);
        }
        $this->categories()->sync($category_ids);
    }


    public function language()
    {
        return $this->belongsTo(Language::class);
    }
    public function media()
    {
        return $this->hasMany(Medium::class);
    }
    public function links()
    {
        return $this->hasMany(Link::class);
    }

    // RELATIONSHIPS BETWEEN ITEMS
    // 1. Parents retrieves all relationships where item is is the subject
    public function parents()
    {
        return $this->belongsToMany(Item::class, 'item_relationships', 'subject_item_id', 'direct_object_item_id')->withPivot('relationship');
    }
    public function hasParents()
    {
        return ($this->parents->count() > 0);
    }
    // 2. Children retrieves all relationships where item is the object
    public function children()
    {
        return $this->belongsToMany(Item::class, 'item_relationships', 'direct_object_item_id', 'subject_item_id')->withPivot('relationship');
    }
    public function hasChildren()
    {
        return ($this->children->count() > 0);
    }

    // 3. Combines parents and children into nautral relationships
    private function reverseRelationship($relationship)
    {
        switch ($relationship) {
            case 'update':
                return 'master';
            default:
                return $relationship;
        }
    }

    public function hasRelationShips()
    {
        return ($this->hasParents() || $this->hasChildren());
    }
    public function relationships()
    {
        $relationships = [];

        foreach ($this->children as $child) {
            $relationships[] = [
                'relationship' => $child->pivot->relationship,
                'item' => $child->id
            ];
        }
        foreach ($this->parents as $parent) {
            $relationships[] = [
                'relationship' => $this->reverseRelationship($parent->pivot->relationship),
                'item' => $parent->id
            ];
        }
        return $relationships;
    }

    public function setRelationships($relationship)
    {
        if (!$relationship)
            return;

        $this->parents()->detach();
        if ($relationship['type'] == 'nothing' || $relationship['item'] == 'nothing') {
            $this->update(['type' => 'master']);
        } else {
            $this->update(['type' => $relationship['type']]);
            $this->parents()->attach($relationship['item'], ['relationship' => $relationship['type']]);
        }
    }


    // STRINGIFIERS
    // 1. stringify timestamps
    private function formattedTimestamps()
    {
        return "Created on {$this->created_at->format('d/m/Y')} | Last updated on {$this->updated_at->format('d/m/Y')}";
    }

    // 2. stringify children as list
    private function stringChildren()
    {
        $children = array_map(function ($item) {
            return "<li>{$item['title']} (item #{$item['id']} | applied as {$item['type']})";
        }, $this->children->toArray());

        return implode("", $children);
    }

    // 3. List all relationships in HTML list
    public function listRelationships()
    {
        return $this->hasChildren() ?
            "<div>
                <p>The following items have a relationship with this item:</p>
                <ul class='unordered-list'>{$this->stringChildren()}</ul>
            </div>" : "";
    }

    // 4. Message when deleting deleting item
    public function confirmDelete()
    {
        $text = "<span>Are you sure you want to delete item #{$this->id} ({$this->title})?";
        if ($this->hasChildren()) {
            $text .=
                "<br>Doing so would remove the following relationships:</span>
                <ul class='unordered-list'>{$this->stringChildren()}</ul>";
        } else {
            $text .= "</span>";
        }
        return $text;
    }

    // 5. Subheader on every item
    public function details()
    {
        return
            "Item #{$this->id} ({$this->title})            
            <br>
            Has {$this->children->count()} dependencies | {$this->formattedTimestamps()}";
    }


    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->slug = Str::slug($model->title);
        });
    }

}
