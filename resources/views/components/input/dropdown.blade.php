<label for="{{ $id }}" class="text-input dropdown @if($tooltip) tooltip @endif">
    @if ($tooltip)
        <x-info-tooltip text="{!! __($tooltip) !!}" />
    @endif
    <h5 class="label">{{ __($label) }}</h5>
    <select class="dropdown-input" id="{{ $id }}" name="{{ $id }}">
        {{ $slot }}
    </select>
</label>