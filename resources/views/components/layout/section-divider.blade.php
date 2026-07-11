<fieldset {{ $attributes->merge(['class' => 'section-divider']) }}>
    <legend>
        <h4>
            {{ $title }}
        </h4>
    </legend>
    {{ $slot }}
</fieldset>