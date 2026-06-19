<span class="poppable-balloon">
    <p>{{ $value }}</p>
    <label>
        <input 
            type="checkbox" 
            name="{{ $name }}" 
            value="{{ $value }}" 
            class="category-option"
            @if($checked) checked @endif
        />
        {!! file_get_contents(filename: 'styles/icons/x_icon.svg') !!}
    </label>
</span>