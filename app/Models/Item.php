<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasMedia;


    // BASE PROPS & METHODS   
    // Fillable properties (update allowed after creation)
    protected $fillable = [
        'title',
        'language',
        'type',
        'language_id',
        'updated_at',
        'file_type'
    ];

    // Boot method includes logic to fill the slug field
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $slug = Str::slug($model->title);
            $originalSlug = $slug;
            $count = 1;

            while (static::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }
            $model->slug = $slug;
        });
    }

    // RELATIONSHIPS
    // Categories: many (categories) to many (items)
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // Shorthand to fetch only visible categories
    // Hidden categories can be used to make special collections on a site
    public function visibleCategories()
    {
        return $this->belongsToMany(Category::class)->where('hidden', '0');
    }

    // Method to sync categories on item update
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

    // Content Blocks = html enabled description of an item
    // many (content blocks) to 1 (item)
    public function contentBlocks()
    {
        return $this->morphMany(ContentBlock::class, 'contentable');
    }
    // Items will likely only have 1 content block, so include shorthand to fetch only the first block
    public function firstContentBlock()
    {
        return $this->contentBlocks()->first();
    }

    // Language: 1 (language) to many (items)
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    // Media: many (media) to 1 (item)
    // Example of item with many media: gallery
    public function media()
    {
        return $this->morphMany(Medium::class, 'mediable');
    }

    // Links: many (links) to 1 (item)
    public function links()
    {
        return $this->hasMany(Link::class);
    }

    // RELATIONSHIPS BETWEEN ITEMS
    // Parents retrieves all relationships where item is is the subject
    public function parents()
    {
        return $this->belongsToMany(Item::class, 'item_relationships', 'subject_item_id', 'direct_object_item_id')->withPivot('relationship');
    }
    public function hasParents()
    {
        return ($this->parents->count() > 0);
    }

    // Children retrieves all relationships where item is the object
    public function children()
    {
        return $this->belongsToMany(Item::class, 'item_relationships', 'direct_object_item_id', 'subject_item_id')->withPivot('relationship')->orderBy('created_at', 'desc');
    }
    public function hasChildren()
    {
        return ($this->children->count() > 0);
    }

    // Combine parents and children into neutral relationships
    private function reverseRelationship($relationship)
    {
        switch ($relationship) {
            case 'update':
                return 'master';
            default:
                return $relationship;
        }
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
    public function hasRelationShips()
    {
        return ($this->hasParents() || $this->hasChildren());
    }

    // Method to update relationships
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
    // Return an array of stringified versions of relations
    public function getRelationsAsString()
    {
        return array_map(function ($item) {
            return [
                'text' => __('item.relationships.relationship_format', [
                    'title' => $item['title'],
                    'id' => $item['id'],
                    'type' => $item['type']
                ])
            ];
        }, $this->children->toArray());
    }
    // Helper method to quickly get timestamps
    public function getTimestampsAsString()
    {
        return __(
            'item.properties.timestamps',
            ['created' => $this->created_at->format('d/m/Y'), 'updated' => $this->updated_at->format('d/m/Y'),]
        );
    }

}
