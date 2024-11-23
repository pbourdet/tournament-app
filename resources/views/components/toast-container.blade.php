<div id="toaster" class="relative z-[9999]">
    @foreach(App\Enums\ToastType::cases() as $type)
        @if(session($type->value))
            <x-toast :type="$type" :message="session($type->value)"/>
        @endif
    @endforeach
    <x-toast :type="'info'" :message="''"/>
    <script>
        function triggerToast(type, message) {
            window.dispatchEvent(new CustomEvent('toast-trigger', {
                detail: { type, message }
            }));
        }
    </script>
</div>
