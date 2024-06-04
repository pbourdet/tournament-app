<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <x-section>
        <div class="p-4 sm:p-8 max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </x-section>

    <x-section class="mt-4">
        <div class="p-4 sm:p-8 max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </x-section>

    <x-section class="mt-4">
        <div class="p-4 sm:p-8 max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </x-section>
</x-app-layout>
