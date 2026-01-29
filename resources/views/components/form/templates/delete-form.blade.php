<dialog id="delete-form" class="pink-border">

    <form class="db-form delete" method="POST" action='{{ query_route("{$controller}.destroy", $selected) }}'>
        @csrf
        @method('DELETE')

        <x-nav.exit-button formmethod="dialog" type="button" />

        <header>
            <h3>{{ App\Words\Headers::look_up("{$controller}_delete_header") }}</h3>
        </header>
        <main>{!! $selected->confirmDelete() !!}</main>

        <footer class="form-footer">
            <nav>
                <a class="action-button passive" onclick="document.getElementById('delete-form').close();">Cancel</a>
                <button class="action-button">Confirm</button>
            </nav>
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <x-error-item message="{{ $error }}" />
                @endforeach
            </ul>
        </footer>
    </form>
</dialog>