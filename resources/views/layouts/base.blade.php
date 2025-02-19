<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico?v=1') }}"/>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet"/>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @fluxAppearance

        <style>
            [x-cloak] {display: none;}
        </style>
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        @guest
            @include('layouts.navigation-guest')
        @endguest
        @auth
            @include('layouts.navigation')
        @endauth
        <flux:main class="p-0! max-w-full!">
            @yield('content')
        </flux:main>

        @livewireScripts
        @fluxScripts
        <x-toast-container/>
    </body>
</html>
