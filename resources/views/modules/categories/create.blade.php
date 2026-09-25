@extends('modules.categories.index')

@section('db-form')

    <x-module.container.create :confirm-route="query_route('categories.store')" name="category-create-form"
        :cancel-route="query_route('categories.index')">

        <x-slot:header>
            {!! __('category.header.create') !!}
        </x-slot:header>
        <x-slot:subheader>
            {!! __('category.message.create') !!}
        </x-slot:subheader>
        <x-slot:body>
            <x-module.category :category="$category" />
        </x-slot:body>

    </x-module.container.create>

@endsection