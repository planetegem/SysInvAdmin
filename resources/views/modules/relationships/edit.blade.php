@extends('modules.relationships.index')

@section('db-form')

    <x-module.container.update :confirm-route="query_route('relationships.update', $relationship)" name="relationship-update-form"
        :cancel-route="query_route('relationships.index')" class="relationship">

        <x-slot:header>
            {!! __('relationship.header.update') !!}
        </x-slot:header>
        <x-slot:subheader>
            {!! __('relationship.properties.name', ['id' => $relationship->id, 'name' => $relationship->name]) !!}
            <br>
            @choice('relationship.properties.usage_count', $relationship->getUsageCount(), ['count' => $relationship->getUsageCount()])
            &nbsp;|&nbsp;
            {!! $relationship->getTimestampsAsString() !!}
        </x-slot:subheader>

        <x-slot:body>
            <x-module.relationship :relationship="$relationship" />
        </x-slot:body>

    </x-module.container.update>

    <x-module.container.delete :confirm-route="query_route('relationships.destroy', $relationship)">

        <x-slot:header>
            {!! __('relationship.header.delete') !!}
        </x-slot:header>
        <x-slot:message>
            {!! __('relationship.message.delete', ['id' => $relationship->id, 'name' => $relationship->name]) !!}
            <br>
            @choice('relationship.message.warning_attached_items', $relationship->getUsageCount(), ['count' => $relationship->getUsageCount()])
        </x-slot:message>

    </x-module.container.delete>

@endsection