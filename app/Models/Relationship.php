<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasTimestamps;


class Relationship extends Model
{
    use HasTimestamps;
    
    protected $fillable = [
        'name',
        'type',
        'subject_label',
        'object_label',
        'subject_descriptor',
        'object_descriptor'
    ];

    // ITEM RELATIONS
    public function itemRelationships(): HasMany
    {
        return $this->hasMany(ItemRelationship::class, 'relationship_id');
    }
    public function getUsageCount(): int
    {
        return $this->itemRelationships()->count();
    }
}
