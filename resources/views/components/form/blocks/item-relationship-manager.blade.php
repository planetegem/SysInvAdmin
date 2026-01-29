<x-info-tooltip text="{!! App\Words\Tooltips::look_up('item_relationships') !!}" />
<main class="relationship-manager">
    @if($item->hasChildren())
        {!! $item->listRelationships() !!}

    @else
        <span>Set&nbsp;as</span>
        <x-form.input.dropdown id="relationship[type]" label="Relationship">
            <option value="nothing">
                <span>Nothing</span>
            </option>
            <option value="update" @if($item->type == "update") selected @endif>
                <span>Update</span>
            </option>
        </x-form.input.dropdown>
        <span>to</span>
        <x-form.input.dropdown id="relationship[item]" label="Item name">
            <option value="nothing">
                <span>Nothing</span>
            </option>

            <?php $relatedItem = $item->parents->first(); ?>

            @foreach($possibleRelations as $relation)
                <option value="{{ $relation->id }}" @if($relatedItem && $relatedItem->id == $relation->id) selected @endif>
                    {{ $relation->title }}
                </option>
            @endforeach
        </x-form.input.dropdown>
    @endif
</main>