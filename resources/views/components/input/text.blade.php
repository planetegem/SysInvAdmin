<label for="{{ $name }}" class="text-input @error($name) error @enderror">
    <h5 class="label">
        {!! __($label) !!}
        @if($attributes->has('required')) (*) @endif
    </h5>
    @if($tooltip)
        <x-info-tooltip text="{!! __($tooltip) !!}" />
    @endif
    <input 
        type="text" 
        name="{{ $name }}" 
        id="{{ $name }}"
        value="{!! $value !!}"
        {{ $attributes }}
        @error($name) placeholder="{{ $message }}" @enderror
    />
</label>