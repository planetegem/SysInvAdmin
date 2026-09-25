@props(['relationship'])

<div class="text-and-dropdown-input">

    <x-input.text name="relationship_name" value="{{ $relationship->name }}" label="relationship.input.name"
        tooltip="relationship.tooltip.name" required />
    <x-input.dropdown id="relationship_type" label="relationship.input.type"
        tooltip="relationship.tooltip.type">
        <option value="hierarchical" @selected($relationship->type == 'hierarchical') >{{ __('relationship.types.hierarchical') }}</option>
        <option value="lateral" @selected($relationship->type == 'lateral') >{{ __('relationship.types.lateral') }}</option>
    </x-input.dropdown>

</div>

<x-layout.section-divider title="{{ __('relationship.header.labels') }}">
    <x-input.text name="relationship_subject_label" value="{{ $relationship->subject_label }}"
        label="relationship.input.subject_label" tooltip="relationship.tooltip.subject_label" required />
    <x-input.text name="relationship_object_label" value="{{ $relationship->object_label }}"
        label="relationship.input.object_label" tooltip="relationship.tooltip.object_label" required />
</x-layout.section-divider>

<x-layout.section-divider title="{{ __('relationship.header.descriptors') }}">
    <x-input.text name="relationship_subject_descriptor" value="{{ $relationship->subject_descriptor }}"
        label="relationship.input.subject_descriptor" tooltip="relationship.tooltip.subject_descriptor" required />
    <x-input.text name="relationship_object_descriptor" value="{{ $relationship->object_descriptor }}"
        label="relationship.input.object_descriptor" tooltip="relationship.tooltip.object_descriptor" required />
</x-layout.section-divider>

<script type="module">
    import RelationshipForm from "/js/relationship-form.js";
    const relationshipForm = new RelationshipForm();
</script>