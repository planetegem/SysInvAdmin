@extends('modules.items.index')

@section('db-form')

    <x-module.container.create :confirm-route="query_route('items.store')" name="item-create-form"
        :cancel-route="query_route('items.index')">

        <x-slot:header>
            {!! __('item.header.create') !!}
        </x-slot:header>
        <x-slot:subheader>
            {!! __('item.message.create') !!}
        </x-slot:subheader>
        <x-slot:body>
            <x-module.item :item="$item" />
        </x-slot:body>

    </x-module.container.create>

@endsection