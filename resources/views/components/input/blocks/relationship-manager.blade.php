<x-layout.foldable-divider title="{!! __('relationship.manager.header') !!}">
    <x-info-tooltip text="{!! __('relationship.tooltip.item_manager') !!}" />
    <main class="relationship-manager" id="{{ $id }}">
        <template class="relationship-manager-template">
            <li class="item-relationship attribute-list-item">
                <div class="li-content-container">
                    <span>{!! __('relationship.manager.set_as') !!}</span>
                    <x-input.dropdown id="{{ $id }}[type]" class="relationship-type-selector">
                        <option value="">{!! __('relationship.manager.nothing_selected') !!}</option>
                        @foreach ($possibleRelationships as $relationship)
                            <option value="{{ $relationship['value'] }}">{{ $relationship['label'] }}</option>
                        @endforeach
                    </x-input.dropdown>
                    <x-input.dropdown id="{{ $id }}[item]" class="relationship-item-selector">
                        <option value="">{!! __('relationship.manager.nothing_selected') !!}</option>
                        @foreach ($possibleTargets as $target)
                            <option value="{{ $target->id }}">{{ $target->title }} (#{{ $target->id }})</option>
                        @endforeach
                    </x-input.dropdown>
                    <button type="button" class="remove-relationship remove-li">
                        {!! file_get_contents("styles/icons/backspace_icon.svg") !!}
                    </button>
                </div>
            </li>
        </template>
        <p class="fallback-text">{{ __('relationship.manager.placeholder') }}</p>
        <ol class="attribute-lister">
        </ol>
        <button type="button" class="add-new-relationship secondary-action-button">
            {!! file_get_contents("styles/icons/add_icon.svg") !!}
            <span>{{ __('relationship.manager.add_new') }}</span>
        </button>
    </main>
    <script type="module">
        import RelationshipManager from "/js/relationship-manager.js";
        const relationshipManager = new RelationshipManager(
            "{{ $id }}",
            [
                @foreach ($item->relationships() as $rel)
                    {
                        type: "{{ $rel['relationship']->consolidated_name }}",
                        item: "{{ $rel['item'] }}"
                    },
                @endforeach
            ]
        );
    </script>
</x-layout.foldable-divider>