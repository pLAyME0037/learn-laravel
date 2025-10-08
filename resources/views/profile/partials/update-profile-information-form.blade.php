<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification"
        method="post"
        action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post"
        action="{{ route('profile.update') }}"
        class="mt-6 space-y-6"
        enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="profile_pic"
                :value="__('Profile Picture')" />

            <div class="mt-2 flex items-center space-x-4">
                <x-profile-image
                    alt="{{ $user->username }}"
                    src="{{ $user->profile_picture_url }}"
                    size="xl"
                    :uploadable="true"
                    name="profile_pic"
                    :userId="$user->id" />
            </div>

            <!-- Hidden field for profile picture removal -->
            <input type="hidden"
                id="remove-profile-pic"
                name="remove_profile_pic"
                value="0">
        </div>
        <div>
            <x-input-label for="username"
                :value="__('Username')" />
            <x-text-input id="username"
                name="username"
                type="text"
                class="mt-1 block w-full"
                :value="old('username', $user->username)"
                required
                autofocus
                autocomplete="username" />
            <x-input-error class="mt-2"
                :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="name"
                :value="__('Name')" />
            <x-text-input id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name" />
            <x-input-error class="mt-2"
                :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email"
                :value="__('Email')" />
            <x-text-input id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username" />
            <x-input-error class="mt-2"
                :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        <div>
            <x-input-label for="bio"
                :value="__('Bio')" />
            <x-textarea-input name="bio"
                :value="old('bio', $user->bio)"
                placeholder="Write your article here..."
                rows="10" />
            <x-input-error class="mt-2"
                :messages="$errors->get('bio')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        // Update the remove profile pic button
        document.addEventListener('DOMContentLoaded', function() {
            const removeButton = document.querySelector('button[onclick*="remove-profile-pic"]');
            if (removeButton) {
                removeButton.addEventListener('click', function() {
                    document.querySelector('input[name="remove_profile_pic"]').value = '1';
                    this.style.display = 'none';
                });
            }
        });
    </script>
</section>
