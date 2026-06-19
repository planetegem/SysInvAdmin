<div class="pink-border">
    <form 
        class="db-form update" id="{{ $name }}" 
        method="POST" action='{{ $confirmRoute }}'
        enctype="multipart/form-data" >
        
        @csrf
        @method('PUT')

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

        <main>
            {{ $body }}
        </main>

        <footer class="form-footer">
            <nav>
                <a class="action-button delete" onclick="document.getElementById('delete-form').showModal();">{{ __('button.delete') }}</a>
                <button class="action-button">{{ __('button.confirm_update') }}</button>
            </nav>
        </footer>
    </form>
</div>