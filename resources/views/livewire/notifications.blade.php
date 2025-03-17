<div x-data="{unread: @js($this->user->unreadNotifications->isNotEmpty())}">
    <flux:modal x-on:close="if (unread) $wire.readAll(); unread = false"
                variant="flyout" name="notifications" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Notifications') }}</flux:heading>
        </div>

        @if($this->user->notifications->isEmpty())
            <div>
                <flux:subheading>
                    {{ __('You have no notifications') }}
                </flux:subheading>
            </div>

        @else
            <div>
                <flux:button variant="filled" icon="trash" wire:click="deleteAll" @click="unread = false" class="!text-red-500">
                    {{ __('Delete all') }}
                </flux:button>
            </div>

            @if($this->user->unreadNotifications->isNotEmpty())
                <div class="space-y-4">
                    <flux:subheading>
                        {{ __('New notifications') }}
                        <flux:badge size="sm" variant="pill" color="red">{{ Auth::user()->unreadNotifications->count() }}</flux:badge>
                    </flux:subheading>
                    @foreach(Auth::user()->unreadNotifications as $notification)
                        <div>
                            <p class="text-sm">{{ __($notification->data['translationKey'], $notification->data['translationParams']) }}</p>
                            <p class="text-xs text-zinc-400">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($this->user->readNotifications->isNotEmpty() && $this->user->unreadNotifications->isNotEmpty())
                <flux:separator/>
            @endif

            @if($this->user->readNotifications->isNotEmpty())
                <div class="space-y-4">
                    <flux:subheading>
                        {{ __('Previous notifications') }}
                    </flux:subheading>
                    @foreach(Auth::user()->readNotifications as $notification)
                        <div class="flex items-center space-x-4 rounded-lg">
                            <div>
                                <p class="text-sm text-zinc-400">{{ __($notification->data['translationKey'], $notification->data['translationParams']) }}</p>
                                <p class="text-xs text-zinc-400">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </flux:modal>
</div>
