<?php

namespace App\Models;

use App\Http\Resources\MediumResource;
use App\Traits\HasContentBlocks;
use App\Traits\HasMedia;
use App\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class Item extends Model
{
    use HasMedia;
    use HasTimestamps;
    use HasContentBlocks;


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
    // And logic to delete media before cascading
    protected static function booted(): void
    {
        static::saving(function ($model) {
            // Prevent regenerations if the title hasn't changed on an existing record
            if ($model->isDirty('title')) {
                $slug = Str::slug($model->title);
                $originalSlug = $slug;
                $count = 1;

                while (static::where('slug', $slug)->where('id', '!=', $model->id)->exists()) {
                    $slug = "{$originalSlug}-{$count}";
                    $count++;
                }

                $model->slug = $slug;
            }
        });

        static::deleting(function ($model) {
            $model->media()->get()->each->delete();
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


    // Language: 1 (language) to many (items)
    public function language()
    {
        return $this->belongsTo(Language::class);
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
                'item' => $child
            ];
        }
        foreach ($this->parents as $parent) {
            $relationships[] = [
                'relationship' => $parent->pivot->getRelationshipFor($this->id),
                'item' => $parent
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
        if (!$validatedArrayOfRelationships)
            return;

        $relationships = [];
        foreach ($validatedArrayOfRelationships as $relationship) {
            $itemRelationship = ItemRelationship::createFromFormData($relationship, $this);

            if ($itemRelationship)
                $relationships[] = $itemRelationship->getAttributes();
        }

        // 3. Bulk insert
        if (!empty($relationships))
            DB::table('item_relationships')->insert($relationships);
    }

    // API METHODS
    // Model query scope: determines which relations need to be eager loaded
    public function scopeWithCompanions($query)
    {
        return $query->with($this::getBaseCompanions());
    }

    // Base relations to include in with() to avoid N+1 queries
    public static function getBaseCompanions(): array
    {
        return [
            'contentBlock',
            'categories',
            'links',
            'media.images',
            'media.videos',
            'media.models3d',
            'language',
            'children',
            'parents'
        ];
    }
    // Return the base relations, but then prefixed so they can be used from point POV of another relation
    public static function getPrefixedCompanions($prefix): array
    {
        return array_map(function ($companion) use ($prefix) {
            return "{$prefix}.{$companion}";
        }, Item::getBaseCompanions());
    }

    // STRINGIFIERS
    // Return an array of stringified versions of relations
    public function getRelationshipsAsString()
    {
        return array_map(function ($relationship) {
            return [
                'text' => __('item.relationships.relationship_format', [
                    'relationship' => $relationship['relationship']->descriptor,
                    'target_name' => $relationship['item']->title,
                    'target_id' => $relationship['item']->id
                ])
            ];
        }, $this->relationships());
    }


}
