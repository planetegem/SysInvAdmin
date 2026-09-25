@props(['category'])

<x-input.text name="category_name" value="{{ $category->name }}" label="category.input.name"
    tooltip="category.tooltip.name" required />

<x-input.checkbox name="hidden_category" label="category.input.hidden" tooltip="category.tooltip.hidden"
    :checked="$category->hidden != 0" />

<x-layout.titled-divider>Page properties</x-layout.titled-divider>

<x-input.text name="category_title" value="{{ $category->title }}" label="category.input.title"
    tooltip="category.tooltip.title" />

<x-input.textarea name=" category_description" value="{{ $category->description }}" label="category.input.description"
    tooltip="category.tooltip.description" medium />

<x-layout.titled-divider>SEO properties</x-layout.titled-divider>
<x-input.text name="category_meta_title" value="{{ $category->meta_title }}" label="meta.input.title"
    tooltip="meta.tooltip.title" />
<x-input.textarea name="category_meta_description" value="{{ $category->meta_description }}"
    label="meta.input.description" tooltip="meta.tooltip.description" small />