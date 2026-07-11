@extends('modules.relationships.index')

@section('db-form')

    <x-form.create-form :confirm-route="query_route('relationships.store')" name="relationship-create-form"
        :cancel-route="query_route('relationships.index')" class="relationship">

        <x-slot:header>
            {!! __('relationship.header.create') !!}
        </x-slot:header>
        <x-slot:subheader>
            {!! __('relationship.message.create') !!}
        </x-slot:subheader>
        <x-slot:body>
            <x-form.templates.relationship :relationship="$relationship" />
        </x-slot:body>

    </x-form.create-form>

@endsection