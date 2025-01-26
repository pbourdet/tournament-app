<div>
    <flux:input label="{{ __('Name') }}" wire:model="name"/>
    <flux:button variant="primary" wire:click="update">
        {{ __('Save') }}
    </flux:button>
</div>
