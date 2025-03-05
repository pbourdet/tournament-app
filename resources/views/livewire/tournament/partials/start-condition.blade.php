<li class="flex items-center">
    @if($condition)
        <flux:icon.check-circle class="text-green-500"/>
    @else
        <flux:icon.x-circle class="text-red-500"/>
    @endif
    {{ $text }}
</li>
