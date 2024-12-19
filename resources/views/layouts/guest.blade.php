@extends('layouts.base')

@section('content')
    <div class="flex flex-col items-center pt-0.5 sm:pt-[8vh]">
        {{ $slot }}
    </div>
@endsection
