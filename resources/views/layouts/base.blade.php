<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico?v=1') }}"/>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="@yield('body-class', 'font-sans antialiased')">
        @guest
            @include('layouts.navigation-guest')
        @endguest
        @auth
            @include('layouts.navigation')
        @endauth
        <x-toast-container/>
        <div class="@yield('container-class', 'min-h-screen bg-gray-100')">
            @yield('content')
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.Echo.private('App.Models.User.{{ auth()->id() }}')
                    .notification((notification) => {
                        triggerToast(notification.toastType, notification.message);
                    });
            });
        </script>
    </body>
</html>
