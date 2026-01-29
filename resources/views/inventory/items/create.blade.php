@extends('inventory.items.index')

@section('db-form')

    <x-form.templates.create-form controller="items">

        <!-- BASE -->
        <div class="text-and-dropdown-input">
            <x-form.input.text name="item_title" value="{{ $item->title }}" />

            <?php $languages = App\Models\Language::all(); ?>
            <x-form.input.dropdown id="language_dropdown" label="Language">
                @foreach ($languages as $language)
                    <option value="{{ $language->id }}" @if($item->language_id == $language->id) selected @endif>
                        {{ $language->name }}
                    </option>
                @endforeach
            </x-form.input.dropdown>
        </div>
        <x-form.input.textarea name="item_description" value="{{ $item->description }}" />

        <!-- MEDIA -->
        <x-form.foldable-divider title="Item media" open>
            <x-form.blocks.media-manager :item="$item" />
        </x-form.foldable-divider>

        <!-- CATEGORIES -->
        <?php 
            $categories = $item->categories;
            $selection = array_map(fn($i): string => $i['name'], $categories->toArray());
        ?>
        <x-form.foldable-divider title="Item categories" open>
            <x-form.blocks.category-manager :selected="$selection" />
        </x-form.foldable-divider>
        <x-form.foldable-divider title="Hidden categories">
            <x-form.blocks.category-manager :selected="$selection" type="hidden" />
        </x-form.foldable-divider>


        <!-- LINKS -->
        <?php $links = $item->links; ?>
        <x-form.foldable-divider title="Item links" open>
            <x-form.blocks.link-manager :links="$links" />
        </x-form.foldable-divider>

        <!-- RELATIONSHIP/TYPE -->
        <x-form.foldable-divider title="Item relationships">
            <x-form.blocks.item-relationship-manager :item="$item" />
        </x-form.foldable-divider>

    </x-form.templates.create-form>

@endsection