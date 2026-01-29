<label for="{{ $name }}" class="text-input @error($name) error @enderror">
    <h5 class="label">
        {{ App\Words\Labels::look_up($label) }}
        @if(!$optional) (*) @endif
    </h5>
    @if($tooltip)
        <x-info-tooltip text="{{ App\Words\Tooltips::look_up($name) }}" />
    @endif
    <input 
        type="text" 
        name="{{ $name }}" 
        id="{{ $name }}"
        value="{{ $value }}"
        @if(!$optional) required @endif
        @error($name) placeholder="{{ $message }}" @enderror
    />
    
</label>