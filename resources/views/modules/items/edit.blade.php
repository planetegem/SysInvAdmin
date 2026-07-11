@extends('modules.items.index')

@section('db-form')


    <x-form.update-form :confirm-route="query_route('items.update', $item)" name="item-update-form"
        :cancel-route="query_route('items.index')">

        <x-slot:header>
            {!! __('item.header.update') !!}
        </x-slot:header>
        <x-slot:subheader>
            {!! __('item.properties.name', ['id' => $item->id, 'title' => $item->title]) !!}
            <br>
            {!! __('item.relationships.count', ['count' => count($item->relationships())]) !!}
            &nbsp;|&nbsp;
            {!! $item->getTimestampsAsString() !!} 

        </x-slot:subheader>
        <x-slot:body>
            <x-form.templates.item :item="$item" />
        </x-slot:body>

    </x-form.update-form>
    <x-form.delete-form :confirm-route="query_route('items.destroy', $item)">
        <x-slot:header>
            {!! __('item.header.delete') !!}
        </x-slot:header>
        <x-slot:message>
            <span>{{ __('item.message.delete', ['id' => $item->id, 'title' => $item->title]) }}</span>

            @if($item->hasChildren())
                <br>
                <span>{{ __('item.relationships.warning_relationships') }}</span>
                <ul class="unordered-list">
                    @foreach($item->getRelationshipsAsString() as $rel)
                        <li>{{ $rel['text'] }}</li>
                    @endforeach
                </ul>
            @endif
        </x-slot:message>
    </x-form.delete-form>


@endsection