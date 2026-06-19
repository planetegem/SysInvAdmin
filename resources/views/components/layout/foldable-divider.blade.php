<details {{ $attributes->merge(['class' => 'foldable-divider']) }}>
    <summary>
        <h4>
            {!! file_get_contents(filename: 'styles/icons/double_arrow_right.svg') !!}
            {{ $title }}
        </h4>
    </summary>
    <section class="large-bordered-input-element">
        {{ $slot }}
    </section>
</details>