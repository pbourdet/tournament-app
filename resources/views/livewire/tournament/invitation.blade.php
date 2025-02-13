<div class="flex flex-col">
    @if($tournament->invitation()->doesntExist())
        <div class="text-xs">
            {{ __('No invitation code for this tournament') }}
        </div>
        <div>
            <flux:button wire:click="refresh" size="sm" icon-trailing="plus">
                {{ __('New code') }}
            </flux:button>
        </div>
    @elseif($tournament->invitation->isExpired())
        <div class="text-xs text-red-500">
            {{ __('Invitation code has expired.') }}
        </div>
        <div>
            <flux:button wire:click="refresh" size="sm" icon-trailing="arrow-path">
                {{ __('Refresh code') }}
            </flux:button>
        </div>
    @else
        <div class="text-xs text-gray-500">
            {{ __('Share this code to other players') }}
        </div>
        <div class="flex gap-1 items-center">
            <flux:input readonly copyable variant="filled" :value="$tournament->invitation?->code" class="max-w-32"/>
            <flux:button wire:click="refresh" size="sm" square icon="arrow-path"/>
            <flux:button variant="danger" wire:click="delete" size="sm" square icon="trash"/>
        </div>
        <div class="text-xs">
            {{ __('Code valid until') }} {{ $tournament->invitation?->expires_at->translatedFormat('l d F H:i') }}
        </div>
    @endif
</div>
