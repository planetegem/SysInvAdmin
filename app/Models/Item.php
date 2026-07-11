<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class Item extends Model
{
    use HasMedia;
    use HasTimestamps;


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
        return $this->belongsToMany(Item::class, 'item_relationships', 'subject_item_id', 'direct_object_item_id')
            ->using(ItemRelationship::class)
            ->withPivot('relationship_id')
            ->withTimestamps();
    }
    public function hasParents()
    {
        return $this->parents()->exists();
    }

    // Children retrieves all relationships where item is the object
    public function children()
    {
        return $this->belongsToMany(Item::class, 'item_relationships', 'direct_object_item_id', 'subject_item_id')
            ->using(ItemRelationship::class)
            ->withPivot('relationship_id')
            ->withTimestamps();
    }
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    // Relationships retrieves all relationships, both parents and children
    // and expresses their relationships
    public function relationships()
    {
        $relationships = [];

        foreach ($this->children as $child) {
            $relationships[] = [
                'relationship' => $child->pivot->getRelationshipFor($this->id),
                'item' => $child->id
            ];
        }
        foreach ($this->parents as $parent) {
            $relationships[] = [
                'relationship' => $parent->pivot->getRelationshipFor($this->id),
                'item' => $parent->id
            ];
        }
        return $relationships;
    }
    public function hasRelationShips()
    {
        return $this->hasParents() || $this->hasChildren();
    }

    // Update relationships
    public function setRelationships(array $validatedArrayOfRelationships)
    {
        DB::table('item_relationships')
            ->where('subject_item_id', $this->id)
            ->orWhere('direct_object_item_id', $this->id)
            ->delete();

        // 2. Convert validated data array into ItemRelationship objects
        if (!$validatedArrayOfRelationships) return;

        $relationships = [];
        foreach($validatedArrayOfRelationships as $relationship){
            $itemRelationship = ItemRelationship::createFromFormData($relationship, $this);

            if ($itemRelationship) $relationships[] = $itemRelationship->getAttributes();
        }

        // 3. Bulk insert
        if (!empty($relationships)) DB::table('item_relationships')->insert($relationships);
    }

    // API METHODS
    // Return item as preformatted object that can be converted to JSON object
    public function asArrayResource($include = ['description', 'media', 'categories', 'links', 'relationships']): array
    {
        $item = [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'language' => $this->language,
        ];

        if (in_array('description', $include))
            $item['description'] = $this->contentBlocks()->first()->content;

        if (in_array('media', $include) && $this->file_type)
            $item['media'] = $this->returnMediaAsArray();

        if (in_array('categories', $include) && $this->categories->count() > 0)
            $item['categories'] = $this->categories->map(function (Category $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'hidden' => $category->hidden == 0 ? false : true
                ];
            });

        if (in_array('links', $include) && $this->links->count() > 0)
            $item['links'] = $this->links->map(function (Link $link) {
                return [
                    'anchor' => $link->anchor,
                    'url' => $link->url
                ];
            });


        if (in_array('relationships', $include)) {
            $item['relationships'] = array_map(function ($relationship) {
                $relatedItem = Item::where('id', $relationship['item'])->first();
                return [
                    'relationship' => $relationship['relationship']->label,
                    'item' => $relatedItem->asArrayResource(['media', 'links', 'categories'])
                ];
            }, $this->relationships());
        }

        return $item;
    }


    // STRINGIFIERS
    // Return an array of stringified versions of relations
    public function getRelationshipsAsString()
    {
        return array_map(function ($relationship) {
            $item = Item::where('id', $relationship['item'])->first();
            return [
                'text' => __('item.relationships.relationship_format', [
                    'relationship' => $relationship['relationship']->descriptor,
                    'target_name' => $item->title,
                    'target_id' => $item->id
                ])
            ];
        }, $this->relationships());
    }
    

}
