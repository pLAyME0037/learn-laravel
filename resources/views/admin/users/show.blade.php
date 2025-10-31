<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Edit User') }}
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Back to Users') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-9">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- User Profile Header -->
                    <div class="flex items-start space-x-6">
                        <img class="h-24 w-24 rounded-full"
                            src="{{ $user->profile_picture_url }}"
                            alt="{{ $user->name }}">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                            <p class="text-lg text-gray-600 dark:text-gray-400"><span>@</span>{{ $user->username }}</p>
                            <p class="text-gray-500 dark:text-gray-400">{{ $user->email }}</p>

                            <div class="mt-2 flex flex-wrap gap-2">
                                @forelse ($user->roles as $role)
                                    <span
                                        class="px-3 py-1 text-sm font-semibold rounded-full 
                                        {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' }}">
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </span>
                                @empty
                                    <span
                                        class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                        No Role Assigned
                                    </span>
                                @endforelse
                                <span
                                    class="px-3 py-1 text-sm font-semibold rounded-full 
                                    {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if ($user->email_verified_at)
                                    <span
                                        class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                        Email Verified
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                        Email Not Verified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- User Details Grid -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Personal Information</h3>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Username</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <span>@</span>{{ $user->username }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->email }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verified</dt>
                                <dd
                                    class="mt-1 text-sm {{ $user->email_verified_at ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $user->email_verified_at ? 'Yes - ' . $user->email_verified_at->format('M j, Y g:i A') : 'No' }}
                                </dd>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Account Information</h3>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Roles</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white capitalize">
                                    {{ $user->roles->pluck('name')->implode(', ') ?: 'N/A' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd
                                    class="mt-1 text-sm {{ $user->is_active ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->created_at->format('M j, Y') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->updated_at->format('M j, Y g:i A') }}</dd>
                            </div>
                        </div>
                    </div>

                    <!-- Bio Section -->
                    @if ($user->bio)
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Bio</h3>
                            <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                    {{ $user->bio }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Account Statistics -->
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Account Statistics</h3>
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Logins</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">-</dd>
                            </div>
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Login</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</dd>
                            </div>
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Profile Views</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">-</dd>
                            </div>
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Age</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->created_at->diffForHumans() }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
