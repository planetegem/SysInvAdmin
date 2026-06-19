<label class="add-to-collection text-input">
    <h5 class="label">{{ $label }}</h5>
    <input list="{{ $list }}" id="{{ $id }}" />
    <button onclick="{{ $callback }}">
        {!! file_get_contents(filename: 'styles/icons/double_arrow_right.svg') !!}
    </button>
</label>