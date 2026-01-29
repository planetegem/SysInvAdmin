<label for="{{ $name }}" class="text-input">
    <h5 class="label">
        {{ App\Words\Labels::look_up($name) }}
        @if(!$optional) (*) @endif
    </h5>
    <textarea 
        type="textarea" 
        name="{{ $name }}" 
        id="{{ $name }}"
        @if(!$optional) required @endif
        {{ $attributes }}

    >{!! $value !!}</textarea>
    <x-info-tooltip text="{{ App\Words\Tooltips::look_up($name) }}" />
</label>