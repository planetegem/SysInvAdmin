@extends('layouts.dashboard')

@section('main')

    <main class="module categories">
        <section class="db-overview">
            <header class="db-overview">
                <h4>Relationship Index</h4>
                <x-nav.name-date-toggle />
            </header>

            <nav class="db-overview">
                <div class="container">
                    @foreach($relationships as $rel)
                        <x-nav.index-item name="{{ $rel->name }}" timestamp="{{ $rel->updated_at }}"
                            target="{{ query_route('relationships.show', $rel) }}" 
                            icon="styles/icons/relationship_icon.svg" />
                    @endforeach
                </div>
            </nav>
            <button class="action-button">
                <a href="{{ query_route('relationships.create') }}">Add new relationship</a>
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