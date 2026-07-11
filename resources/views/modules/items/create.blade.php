@extends('modules.items.index')

@section('db-form')

    <x-form.create-form :confirm-route="query_route('items.store')" name="item-create-form"
        :cancel-route="query_route('items.index')">

        <x-slot:header>
            {!! __('item.header.create') !!}
        </x-slot:header>
        <x-slot:subheader>
            {!! __('item.message.create') !!}
        </x-slot:subheader>
        <x-slot:body>
            <x-form.templates.item :item="$item" />
        </x-slot:body>

    </x-form.create-form>

@endsection