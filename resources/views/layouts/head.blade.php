<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SysInvAdmin</title>

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('styles/base.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('styles/form/form.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('styles/form/input.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('styles/form/layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('styles/form/blocks.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('styles/blade-components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('styles/css-components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('styles/auth.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('styles/main.css') }}">

    <script src="{{ URL::asset('script.js') }}" defer></script>
    <script src="{{ URL::asset('js/category-manager.js') }}" defer></script>
    <script src="{{ URL::asset('js/media-manager.js') }}" defer></script>


</head>
@yield('body')

</html>