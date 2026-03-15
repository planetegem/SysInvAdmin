@extends('layouts.head')

@section('body')
    <body class="main" onload="document.querySelectorAll('.open.modal').forEach((e) => e.showModal());">
        <header class="dashboard">

            <nav class="top-left-header">
                <x-logo.small />
            </nav>

            <nav class="top-right-header">
                <a class="top-right-header" href="/api/documentation" target="_self">documentation</a>

                @if (Auth::check() && auth()->user()->role != 'user')
                    <a class="top-right-header" href="/admin" target="_self">admin</a>
                @endif

                <a class="top-right-header" href="/settings" target="_self">settings</a>

                <form method="POST" action="{{ route('login.destroy') }}">
                    @csrf
                    <button class="action-button">Sign out</button>
                </form>

            </nav>
        </header>

        <aside>
            <nav class="inventory-menu">
                <ul>
                    <x-menu-item target="items" icon="styles/icons/item_icon.svg"/>
                    <x-menu-item target="categories" icon="styles/icons/category_icon.svg"/>
                    <x-menu-item target="media" icon="styles/icons/media_icon.svg"/>
                </ul>
            </nav>
        </aside>
        
        @yield('main')

    </body>
@endsection