<x-info-tooltip text="{!! __('tooltip.item.relations') !!}" />
<main class="relationship-manager">

    <span>Set&nbsp;as</span>
    <x-input.dropdown id="relationship[type]" label="Relationship">
        <option value="nothing">
            <span>Nothing</span>
        </option>
        <option value="update" @if($item->type == "update") selected @endif>
            <span>Update</span>
        </option>
    </x-input.dropdown>
    <span>to</span>
    <x-input.dropdown id="relationship[item]" label="Item name">
        <option value="nothing">
            <span>Nothing</span>
        </option>

        <?php $relatedItem = $item->parents->first(); ?>

        @foreach($possibleRelations as $relation)
            <option value="{{ $relation->id }}" @if($relatedItem && $relatedItem->id == $relation->id) selected @endif>
                {{ $relation->title }}
            </option>
        @endforeach
    </x-input.dropdown>

</main>