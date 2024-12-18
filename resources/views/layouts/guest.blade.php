@extends('layouts.base')

@section('content')
    <div class="flex flex-col items-center pt-[8vh]">
        {{ $slot }}
    </div>
@endsection
