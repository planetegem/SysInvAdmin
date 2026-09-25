@extends('layouts.dashboard')

@section('main')

    <main class="module media">
        <section class="db-overview">
            <header class="db-overview">
                <h4>Media Index</h4>
                <x-nav.name-date-toggle />
            </header>

            <nav class="db-overview">
                <div class="container">
                    @foreach($media as $med)
                        <x-nav.index-item name="{{ $med->name }}" timestamp="{{ $med->updated_at }}"
                            target="{{ query_route('media.show', $med) }}" 
                            icon="{{ $med->icon }}" />
                    @endforeach
                </div>
            </nav>
            <button class="action-button">
                <a href="{{ query_route('media.create') }}">Add new medium</a>
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