@props(['src', 'alt' => 'Profile Image', 'size' => 'md', 'uploadable' => false, 'name' => 'avatar'])

@php
    // Container Dimensions
    $sizeClasses =
        [
            'xs' => 'w-10 h-10',
            'sm' => 'w-16 h-16',
            'md' => 'w-24 h-24',
            'lg' => 'w-32 h-32',
            'xl' => 'w-40 h-40',
            '2xl' => 'w-48 h-48',
        ][$size] ?? 'w-24 h-24';

    // Icon Dimensions
    $iconSize =
        [
            'xs' => 'w-3 h-3',
            'sm' => 'w-4 h-4',
            'md' => 'w-5 h-5',
            'lg' => 'w-6 h-6',
            'xl' => 'w-6 h-6',
            '2xl' => 'w-8 h-8',
        ][$size] ?? 'w-5 h-5';
@endphp

<div class="relative inline-block"
    x-data="{
        imageUrl: '{{ $src }}',
        uploading: false,
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;
    
            this.uploading = true;
    
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imageUrl = e.target.result;
                this.uploading = false;
            };
            reader.readAsDataURL(file);
        }
    }">

    <!-- Main Image Container -->
    <div
        class="{{ $sizeClasses }} relative rounded-full ring-4 ring-white dark:ring-gray-800 shadow-sm bg-gray-100 dark:bg-gray-700 group">

        <!-- The Image -->
        <img :src="imageUrl"
            alt="{{ $alt }}"
            class="w-full h-full rounded-full object-cover transition-opacity duration-300"
            :class="{ 'opacity-50': uploading }">

        <!-- Loading Overlay (Only shows while processing) -->
        <div x-show="uploading"
            class="absolute inset-0 flex items-center justify-center rounded-full bg-black/30 backdrop-blur-sm transition-all"
            x-transition.opacity>
            <svg class="animate-spin {{ $iconSize }} text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"></circle>
                <path class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        </div>

        <!-- Camera Trigger Button -->
        @if ($uploadable)
            <button type="button"
                @click="$refs.fileInput.click()"
                class="absolute bottom-0 right-0 p-2 
                       bg-white dark:bg-gray-800 
                       text-gray-600 dark:text-gray-200 
                       rounded-full shadow-md border border-gray-200 dark:border-gray-600
                       hover:text-blue-600 hover:border-blue-400 dark:hover:text-blue-400
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                       transition-all duration-200 ease-in-out transform hover:scale-105"
                title="Upload new photo">

                <svg class="{{ $iconSize }}"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>

            <!-- HIDDEN Input (Required for form submission) -->
            <input type="file"
                name="{{ $name }}"
                x-ref="fileInput"
                class="hidden"
                accept="image/png, image/jpeg, image/jpg"
                @change="handleFileSelect"
                {{ $attributes }}>
        @endif
    </div>
</div>
