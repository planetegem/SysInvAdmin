
<li class="{{ $class }} menu-item">
    <a href='{{ route("{$target}.index") }}' target="_self">
        {!! file_get_contents($icon) !!}
        <h2>{{ ucfirst($target) }}</h2>
    </a>
</li>