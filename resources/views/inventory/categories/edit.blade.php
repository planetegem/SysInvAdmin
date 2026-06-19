@extends('inventory.categories.index')

@section('db-form')

    <x-form.update-form :confirm-route="query_route('categories.update', $category)" name="category-update-form"
        :cancel-route="query_route('categories.index')">

        <x-slot:header>
            {!! __('category.header.update') !!}
        </x-slot:header>
        <x-slot:subheader>
            {!! __('category.properties.name', ['id' => $category->id, 'name' => $category->name]) !!}
            <br>
            {!! __('category.properties.item_count', ['count' => $category->items()->count()]) !!}
            &nbsp;|&nbsp;
            {!! $category->getTimestampsAsString() !!} </x-slot:subheader>
        <x-slot:body>
            <x-form.templates.category :category="$category" />
        </x-slot:body>

    </x-form.update-form>

    <x-form.delete-form :confirm-route="query_route('categories.destroy', $category)">

        <x-slot:header>
            {!! __('category.header.delete') !!}
        </x-slot:header>
        <x-slot:message>
            {!! __('category.message.delete', ['id' => $category->id, 'name' => $category->name]) !!}
            <br>
            {!! __('category.message.warning_attached_items', ['count' => $category->items()->count()]) !!}
        </x-slot:message>

    </x-form.delete-form>

@endsection