<div class="pink-border">
    <form class="db-form create" id="{{ $controller }}-form" method="POST"
        action='{{ query_route("{$controller}.store") }}' enctype="multipart/form-data">
        @csrf
        <x-nav.exit-button type="anchor" href='{{ query_route("{$controller}.index") }}' />

        <header>
            <h3>{{ App\Words\Headers::look_up("{$controller}_create_header") }}</h3>
            <p>{{ App\Words\Headers::look_up("{$controller}_create_intro") }}</p>
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <x-error-item message="{{ $error }}" />
                @endforeach
            </ul>
        </header>

        <main>{{ $slot }}</main>

        <footer class="form-footer">
            <nav>
                <a href='{{ query_route("{$controller}.index") }}' class="action-button passive">Cancel</a>
                <button class="action-button">Save</button>
            </nav>

        </footer>
    </form>
</div>