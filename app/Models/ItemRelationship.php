<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ItemRelationship extends Pivot
{
    // Primary key is incrementing (Laravel presumes false otherwise)
    public $incrementing = true;

    // Table that model belongs to
    protected $table = 'item_relationships';

    // Foreign keys
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class, 'relationship_id');
    }
    public function subjectItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'subject_item_id');
    }
    public function directObjectItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'direct_object_item_id');
    }

    // Get relationship details based on perspective
    public function getRelationshipFor(int $itemId)
    {
        $rel = $this->relationship;

        if (!$rel)
            return (object) ['label' => 'related_to', 'descriptor' => 'related_to'];

        $isObject = ($this->direct_object_item_id === $itemId);
        $prefix = $isObject ? 'subject' : 'object';
        $label = $rel->{"{$prefix}_label"};
        $descriptor = $rel->{"{$prefix}_descriptor"};

        return (object) [
            'label' => $label,
            'descriptor' => $descriptor,
            'name' => $rel->name,
            'consolidated_name' => $rel->name . '|' . $label,
        ];
    }

    // Fillable props for mass assignment
    protected $fillable = [
        'relationship_id',
        'subject_item_id',
        'direct_object_item_id'
    ];

    // Make ItemRelationship instance from form request
    public static function createFromFormData(array $validatedRow, Item $currentItem): ?self {
        // Parse type to establish relationship type
        if (!str_contains($validatedRow['type'] ?? '', '|')) return null;
        [$relationshipName, $chosenLabel] = explode('|', $validatedRow['type']);

        $relationshipType = Relationship::where('name', $relationshipName)->first();
        if (!$relationshipType) return null;

        // Get target item
        $targetId = $validatedRow['item'];

        // Get direction of relationship
        if ($relationshipType->subject_label === $chosenLabel) {
            $subjectId = $currentItem->id;
            $objectId  = $targetId;
        } else {
            $subjectId = $targetId;
            $objectId  = $currentItem->id;
        }

        // Return instance of self
        $instance = new self([
            'relationship_id'       => $relationshipType->id,
            'subject_item_id'       => $subjectId,
            'direct_object_item_id' => $objectId,
        ]);
        $instance->updateTimestamps();

        return $instance;
    }
}
