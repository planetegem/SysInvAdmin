<a href="{{ $target }}" class="index-item {{ $class }}">
    <h5>{!! $name !!}</h5>
    <span>{{ $timestamp }}</span>
    {!! file_get_contents($icon) !!}
</a>
