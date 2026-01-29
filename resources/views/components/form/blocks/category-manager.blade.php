<x-info-tooltip text='{{ App\Words\Tooltips::look_up($prefix . "item_categories") }}' />
<main class="category-manager">
    <x-form.input.list-selector 
            label="{{ App\Words\Labels::look_up($prefix . 'item_categories') }}" 
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
                <x-form.poppable-balloon value="test" name="{{ $prefix }}categories[]" />
            </template>
            @foreach ($allCategories as $category)
                <?php $checked = in_array($category->name, $selectedCategories); ?>
                <x-form.poppable-balloon value="{{ $category->name }}" name="{{ $prefix }}categories[]" :checked="$checked" />
            @endforeach
        </div>
    </fieldset>
</main>