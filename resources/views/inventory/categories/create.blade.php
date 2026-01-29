@extends('inventory.categories.index')

@section('db-form')

    <x-form.templates.create-form controller="categories">

        <x-form.input.text name="category_name" value="{{ $category->name }}" />
        <x-form.input.checkbox name="hidden_category" checked="{{ $category->hidden }}"/>

        <x-form.titled-divider>Page properties</x-form.titled-divider>
        <x-form.input.text name="category_title" value="{{ $category->title }}" optional />
        <x-form.input.textarea name="category_description" value="{{ $category->description }}" optional medium />

        <x-form.titled-divider>SEO properties</x-form.titled-divider>
        <x-form.input.text name="category_meta_title" value="{{ $category->meta_title }}" optional />
        <x-form.input.textarea name="category_meta_description" value="{{ $category->meta_description }}" optional small />

    </x-form.templates.create-form>

@endsection