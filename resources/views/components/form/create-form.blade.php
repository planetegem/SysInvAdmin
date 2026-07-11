<div class="pink-border">
    <form {{ $attributes->merge(['class' => 'db-form create']) }} id="{{ $name }}" method="POST"
        action='{{ $confirmRoute }}' enctype="multipart/form-data">
        @csrf

        @if ($cancelRoute)
            <x-nav.exit-button type="anchor" href='{{ $cancelRoute }}' />
        @endif

        <header>
            @if ($header)
                <h3>{!! $header !!}</h3>
            @endif ($header)
            @if ($subheader)
                <p>{!! $subheader !!}</p>
            @endif
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <x-error-item message="{{ $error }}" />
                @endforeach
            </ul>
        </header>

        <main>{{ $body }}</main>

        <footer class="form-footer">
            <nav>
                @if ($cancelRoute)
                    <a href='{{ $cancelRoute }}' class="action-button passive">{{ __('button.cancel') }}</a>
                @endif
                <button class="action-button">{{ __('button.confirm_create') }}</button>
            </nav>
        </footer>
    </form>
</div>