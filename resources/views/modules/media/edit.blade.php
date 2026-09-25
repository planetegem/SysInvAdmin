@extends('modules.media.index')

@section('db-form')

    <x-module.container.update :confirm-route="query_route('media.update', $medium)" name="medium-update-form"
        :cancel-route="query_route('media.index')" class="medium">

        <x-slot:header>
            {!! __('media.module.header.update') !!}
        </x-slot:header>
        <x-slot:subheader>
            {!! $medium->name !!}
            <br>
            {!! $medium->getTimestampsAsString() !!}
        </x-slot:subheader>
        <x-slot:body>
            <x-module.medium :medium="$medium" />
        </x-slot:body>

    </x-module.container.update>

    <x-module.container.delete :confirm-route="query_route('media.destroy', $medium)">

        <x-slot:header>
            {!! __('media.module.header.delete') !!}
        </x-slot:header>
        <x-slot:message>
            <span>{{ __('media.module.message.delete', ['name' => $medium->name]) }}</span>
        </x-slot:message>

    </x-module.container.delete>

@endsection