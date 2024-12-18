@extends('layouts.base')

@section('content')
    <div>
        {{ $slot }}
    </div>

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.Echo.private('App.Models.User.{{ auth()->id() }}')
                    .notification((notification) => Flux.toast({text: notification.message, variant: notification.toastType}))
                ;
            });
        </script>
    @endauth
@endsection
