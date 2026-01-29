@extends('layouts.dashboard')

@section('main')

    <main class="inventory under_construction">
        <div>
            {!! file_get_contents("styles/icons/construction_icon.svg") !!}
            <p>Under Construction</p>
        </div>

    </main>

@endsection