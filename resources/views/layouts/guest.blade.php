@extends('layouts.base')

@section('body-class', 'font-sans text-gray-900 antialiased')
@section('container-class', 'min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100')

@section('content')
    <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
@endsection
