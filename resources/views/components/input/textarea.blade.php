<label for="{{ $name }}" class="text-input">
    <h5 class="label">
        {!! __($label) !!}
        @if($attributes->has('required')) (*) @endif
    </h5>
    <textarea 
        type="textarea" 
        name="{{ $name }}" 
        id="{{ $name }}"
        {{ $attributes }}

    >{!! $value !!}</textarea>
    
    @if($tooltip)
        <x-info-tooltip text="{!! __($tooltip) !!}" />
    @endif
</label>