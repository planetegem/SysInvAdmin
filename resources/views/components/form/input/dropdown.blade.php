<label for="{{ $id }}" class="text-input dropdown @if($tooltip) tooltip @endif">
    @if ($tooltip)
        <x-info-tooltip text="{!! App\Words\Tooltips::look_up($id) !!}" />
    @endif
    <h5 class="label">{{ $label }}</h5>
    <select class="dropdown-input" id="{{ $id }}" name="{{ $id }}">
        {{ $slot }}
    </select>
</label>