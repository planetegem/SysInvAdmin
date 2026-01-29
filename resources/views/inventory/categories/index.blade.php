@extends('layouts.dashboard')

@section('main')

    <main class="inventory categories">
        <section class="db-overview">
            <header class="db-overview">
                <h4>Category Index</h4>
                <x-nav.name-date-toggle />
            </header>

            <nav class="db-overview">
                <div class="container">
                    @foreach($categories as $cat)
                        <x-nav.index-item name="{{ $cat->name }}" timestamp="{{ $cat->updated_at }}"
                            target="{{ query_route('categories.show', $cat) }}" 
                            icon="{{ $cat->hidden ? 'styles/icons/hidden_icon.svg' : 'styles/icons/category_icon.svg' }}" />
                    @endforeach
                </div>
            </nav>
            <button class="action-button">
                <a href="{{ query_route('categories.create') }}">Add new category</a>
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