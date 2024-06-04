@extends('layouts.base')

@section('body-class', 'font-sans antialiased')
@section('container-class', 'min-h-screen bg-gray-100')
@section('content')
    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main class="py-12">
        {{ $slot }}
    </main>
@endsection
