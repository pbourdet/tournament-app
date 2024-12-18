@extends('layouts.base')

@section('content')
    <div class="flex flex-col items-center pt-[10vh]">
        {{ $slot }}
    </div>
@endsection
