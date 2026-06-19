<label class="checkbox">
    <input type="hidden" name="{{ $name }}" value="0" />
    <input type="checkbox" name="{{ $name }}" @checked($checked ?? false) {{ $attributes }} />
    <h5>{{ __($label) }}</h5>

    @if ($tooltip)
        <x-info-tooltip text="{!! __($tooltip) !!}" />
    @endif
</label>