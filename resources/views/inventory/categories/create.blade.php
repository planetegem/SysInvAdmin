@extends('inventory.categories.index')

@section('db-form')

    <x-form.create-form :confirm-route="query_route('categories.store')" name="category-create-form"
        :cancel-route="query_route('categories.index')">

        <x-slot:header>
            {!! __('category.header.create') !!}
        </x-slot:header>
        <x-slot:subheader>
            {!! __('category.message.create') !!}
        </x-slot:subheader>
        <x-slot:body>
            <x-form.templates.category :category="$category" />
        </x-slot:body>

    </x-form.create-form>

@endsection