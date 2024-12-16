@extends('layouts.base')

@section('body-class', 'font-sans antialiased')
@section('container-class', 'min-h-screen bg-gray-100')
@section('content')

    {{ $slot }}

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.Echo.private('App.Models.User.{{ auth()->id() }}')
                    .notification((notification) => triggerToast(notification.toastType, notification.message))
                ;
            });
        </script>
    @endauth
@endsection
