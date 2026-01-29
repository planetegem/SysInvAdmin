@props([
    'type' => 'button'    
])

@if ($type == "button")
    <button {{ $attributes->merge(['class' => 'exit-button']) }}>
        {!! file_get_contents(filename: 'styles/icons/x_icon.svg') !!}
    </button>
@elseif ($type == "anchor")
    <a {{ $attributes->merge(['class' => 'exit-button']) }}>
        {!! file_get_contents(filename: 'styles/icons/x_icon.svg') !!}
    </a>
@endif