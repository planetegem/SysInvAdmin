<label for="{{ $id }}" @class(['text-input', 'dropdown', 'tooltip' => $tooltip, 'labeled' => $label]) >
    @if ($tooltip)
        <x-info-tooltip text="{!! __($tooltip) !!}" />
    @endif
    @if ($label)
        <h5 class="label">{{ __($label) }}</h5>
    @endif
    <select id="{{ $id }}" name="{{ $id }}" {{ $attributes->merge(['class' => 'dropdown-input']) }}>
        {{ $slot }}
    </select>
</label>