<x-info-tooltip text='{{ __($tooltip) }}' />
<main class="category-manager">
    <x-input.list-selector 
            label="{{ __($label) }}" 
            id="{{ $prefix }}category-collection-input" 
            list="{{ $prefix }}category-collection"
            callback="addCategory(event, '{{ $prefix }}');" />
    <datalist id="{{ $prefix }}category-collection">
        @foreach ($allCategories as $category)
            <option value="{{ $category->name }}"></option>
        @endforeach
    </datalist>
    <fieldset id="{{ $prefix }}item-categories">
        <legend>Selected categories</legend>
        <div class="selected-categories-container" id="{{ $prefix }}category_selection">
            <template id="{{ $prefix }}example-category">
                <x-input.poppable-balloon value="test" name="{{ $prefix }}categories[]" />
            </template>
            @foreach ($allCategories as $category)
                <?php $checked = in_array($category->name, $selectedCategories); ?>
                <x-input.poppable-balloon value="{{ $category->name }}" name="{{ $prefix }}categories[]" :checked="$checked" />
            @endforeach
        </div>
    </fieldset>
</main>