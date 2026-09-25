@extends('modules.media.index')

@section('db-form')

    <x-module.container.create :confirm-route="query_route('media.store')" name="medium-create-form"
        :cancel-route="query_route('media.index')">

        <x-slot:header>
            {!! __('media.module.header.create') !!}

        </x-slot:header>
        <x-slot:subheader>
            {!! __('media.module.message.create') !!}
        </x-slot:subheader>
        <x-slot:body>
            <x-module.medium :medium="$medium" />
        </x-slot:body>

    </x-module.container.create>

@endsection