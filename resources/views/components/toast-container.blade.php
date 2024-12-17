@persist('toast')
    <flux:toast position="top right"/>
@endpersist
@if (null !== session('toast'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Flux.toast({text: '{{ session('toast')['text'] }}', variant: '{{ session('toast')['variant'] }}'});
        });
    </script>
@endif
