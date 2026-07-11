<dialog id="delete-form" class="pink-border">

    <form {{ $attributes->merge(['class' => 'db-form delete']) }} method="POST" action='{{ $confirmRoute }}'>
        @csrf
        @method('DELETE')

        <x-nav.exit-button formmethod="dialog" type="button" />

        <header>
            <h3>{!!$header !!}</h3>
        </header>
        <main>{{ $message }}</main>

        <footer class="form-footer">
            <nav>
                <button class="action-button passive" formmethod="dialog">{{ __('button.cancel') }}</a>
                <button class="action-button">{{ __('button.confirm') }}</button>
            </nav>
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <x-error-item message="{{ $error }}" />
                @endforeach
            </ul>
        </footer>
    </form>
</dialog>