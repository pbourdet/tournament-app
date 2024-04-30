<div class="relative flex items-center" x-data="{
    hasImage: false,
    showPreview(event) {
        const reader = new FileReader();
        reader.onload = (e) => {
            this.$refs.picturePreview.src = e.target.result;
            this.hasImage = true;
        };
        reader.readAsDataURL(event.target.files[0]);
    },
    removeImage() {
        this.$refs.picturePreview.src = '{{ Storage::url('user-picture-placeholder.jpeg') }}';
        this.hasImage = false;
        this.$refs.profilePicture.value = '';
    }
}">
    <div class="rounded-full bg-gray-200 mr-4 relative">
        <img x-ref="picturePreview" src="{{ Storage::url('user-picture-placeholder.jpeg') }}" alt="Profile picture preview" class="w-24 h-24 rounded-full object-cover">
        <button type="button" x-show="hasImage" @click="removeImage" class="absolute top-1 right-1 hover:bg-gray-600 hover:text-white text-black rounded-full" style="transform: translate(50%,-50%);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </button>
    </div>
    <div>
        <x-secondary-button @click="$refs.profilePicture.click()">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"/>
                </svg>
                {{ __('Upload Picture') }}
            </div>
            <input x-ref="profilePicture" @change="showPreview" type="file" name="profile_picture" class="absolute inset-0 opacity-0 -z-10">
        </x-secondary-button>
    </div>
</div>
