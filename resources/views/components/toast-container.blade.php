@foreach(App\Enums\ToastType::cases() as $type)
    @if(session($type->value))
        <x-toast :type="$type"/>
    @endif
@endforeach
