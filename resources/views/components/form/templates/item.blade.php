@props(['item'])
<!-- BASE -->
<div class="text-and-dropdown-input">
    <x-input.text name="item_title" value="{{ $item->title }}" label="item.input.title" tooltip="tooltip.item.title"
        required />

    <?php $languages = App\Models\Language::all(); ?>
    <x-input.dropdown id="language_dropdown" label="Language">
        @foreach ($languages as $language)
            <option value="{{ $language->id }}" @if($item->language_id == $language->id) selected @endif>
                {{ $language->name }}
            </option>
        @endforeach
    </x-input.dropdown>
</div>

<x-input.blocks.quill-editor name="item_description" id="item-description" :content="$item->firstContentBlock()"
    label="item.input.description" tooltip="tooltip.item.description" required />

<!-- MEDIA -->
<x-layout.foldable-divider title="Item media" open>
    <x-input.blocks.media-manager :item="$item" id="item_media" />
</x-layout.foldable-divider>

<!-- CATEGORIES -->
<?php 
                                $categories = $item->categories;
$selection = array_map(fn($i): string => $i['name'], $categories->toArray());
                            ?>
<x-layout.foldable-divider title="Item categories" open>
    <x-input.blocks.category-manager :selected="$selection" label="category.add_category"
        tooltip="tooltip.item.categories" />
</x-layout.foldable-divider>
<x-layout.foldable-divider title="Hidden categories">
    <x-input.blocks.category-manager :selected="$selection" type="hidden" label="category.add_hidden_category"
        tooltip="tooltip.item.hidden_categories" />
</x-layout.foldable-divider>

<!-- LINKS -->
<?php $links = $item->links; ?>
<x-layout.foldable-divider title="Item links" open>
    <x-input.blocks.link-manager :links="$links" />
</x-layout.foldable-divider>

<!-- RELATIONSHIP/TYPE -->
<x-input.blocks.relationship-manager id="item_relationships" :item="$item" />