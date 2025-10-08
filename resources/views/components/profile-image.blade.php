@props([
    'src',
    'alt' => 'Profile Image',
    'size' => 'md',
    'uploadable' => false,
    'name' => 'avatar',
    'userId' => null,
])

@php
    $sizeClasses =
        [
            'xs' => 'w-6 h-6',
            'sm' => 'w-8 h-8',
            'md' => 'w-12 h-12',
            'lg' => 'w-16 h-16',
            'xl' => 'w-24 h-24',
            '2xl' => 'w-32 h-32',
        ][$size] ?? 'w-12 h-12';

    $iconSize =
        [
            'xs' => 'w-3 h-3',
            'sm' => 'w-4 h-4',
            'md' => 'w-5 h-5',
            'lg' => 'w-6 h-6',
            'xl' => 'w-8 h-8',
            '2xl' => 'w-10 h-10',
        ][$size] ?? 'w-5 h-5';
@endphp

<div class="relative inline-block"
    x-data="profileImage()">
    <!-- Profile Image -->
    <div
        class="{{ $sizeClasses }} rounded-full overflow-hidden border-2 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700">
        <img src="{{ $src }}"
            alt="{{ $alt }}"
            class="w-full h-full object-cover"
            x-ref="image"
            @error($name) class="border-red-500" @enderror>
    </div>

    <!-- Upload Button (if uploadable) -->
    @if ($uploadable)
        <button type="button"
            class="absolute -bottom-1 -right-1 bg-blue-500 hover:bg-blue-600 text-white rounded-full p-1 shadow-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
            @click="$refs.fileInput.click()"
            x-tooltip="'Change photo'">
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

        <!-- Hidden File Input -->
        <input type="file"
            name="{{ $name }}"
            x-ref="fileInput"
            class="hidden"
            accept="image/*"
            @change="handleFileSelect">

        <!-- Loading Spinner -->
        <div x-show="uploading"
            class="absolute inset-0 bg-gray-500 bg-opacity-50 rounded-full flex items-center justify-center">
            <svg class="animate-spin {{ $iconSize }} text-white"
                fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4">
                </circle>
                <path class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        </div>
    @endif
</div>

@if ($uploadable)
    @push('scripts')
        <script>
            function profileImage() {
                return {
                    uploading: false,

                    handleFileSelect(event) {
                        const file = event.target.files[0];
                        if (!file) return;

                        // Validate file type
                        if (!file.type.startsWith('image/')) {
                            alert('Please select an image file.');
                            return;
                        }

                        // Validate file size (max 5MB)
                        if (file.size > 5 * 1024 * 1024) {
                            alert('Image must be less than 5MB.');
                            return;
                        }

                        this.uploading = true;

                        // Preview image
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.$refs.image.src = e.target.result;
                            this.uploading = false;
                        };
                        reader.readAsDataURL(file);

                        // You can also automatically submit the form or make an AJAX request here
                        // this.uploadImage(file);
                    },

                    uploadImage(file) {
                        // Example AJAX upload
                        const formData = new FormData();
                        formData.append('avatar', file);
                        formData.append('_token', '{{ csrf_token() }}');
                        @if ($userId)
                            formData.append('user_id', '{{ $userId }}');
                        @endif

                        fetch('{{ route('profile.update') }}', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                this.uploading = false;
                                if (data.success) {
                                    // Update image source with new URL
                                    this.$refs.image.src = data.avatar_url + '?t=' + new Date().getTime();
                                } else {
                                    alert('Failed to upload image: ' + data.message);
                                }
                            })
                            .catch(error => {
                                this.uploading = false;
                                console.error('Error:', error);
                                alert('Error uploading image');
                            });
                    }
                }
            }
        </script>
    @endpush
@endif
