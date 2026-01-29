<div class="pink-border">
    <form 
        class="db-form update" id="{{ $controller }}-form" 
        method="POST" action='{{ query_route("{$controller}.update", $selected) }}'
        enctype="multipart/form-data" >
        
        @csrf
        @method('PUT')

        <x-nav.exit-button type="anchor" href='{{ query_route("{$controller}.index") }}' />

        <header>
            <h3>{{ App\Words\Headers::look_up("{$controller}_update_header") }}</h3>
            <p>{!! $selected->details() !!}</p>
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <x-error-item message="{{ $error }}" />
                @endforeach
            </ul>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="form-footer">
            <nav>
                <a class="action-button delete" onclick="document.getElementById('delete-form').showModal();">Delete</a>
                <button class="action-button">Update</button>
            </nav>
        </footer>
    </form>
</div>