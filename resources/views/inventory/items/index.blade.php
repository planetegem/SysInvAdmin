@extends('layouts.dashboard')

@section('main')

    <main class="inventory categories">
        <section class="db-overview">
            <header class="db-overview">
                <h4>Item Index</h4>
                <x-nav.name-date-toggle />
            </header>

            <nav class="db-overview">
                <div class="container">
                    @foreach($items as $it)
                        <x-nav.index-item name="{{ $it->title }}" timestamp="{{ $it->updated_at }}"
                            target="{{ query_route('items.show', $it) }}"                             
                            icon="{{ ($it->type == 'update') ? 'styles/icons/update_icon.svg' : 'styles/icons/simple_item_icon.svg' }}" />
                    @endforeach
                </div>
            </nav>
            <button class="action-button">
                <a href="{{ query_route('items.create') }}">Add new item</a>
            </button>
        </section>
        <section class="db-form">
            @yield('db-form')
        </section>

        @if (session('succes'))
            <x-return-message message="{{ session('succes') }}" type="succes"/>
        @endif

    </main>

@endsection