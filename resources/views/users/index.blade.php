<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User Management') }}
            </h2>
            <a href="{{ route('admin.users.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Add New User') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET"
                        action="{{ route('admin.users.index') }}"
                        class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <x-input-label for="search"
                                    :value="__('Search')" />
                                <x-text-input id="search"
                                    name="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('search', $search)"
                                    placeholder="Search by name, email, or username" />
                            </div>

                            <!-- Role Filter -->
                            <div>
                                <x-input-label for="role"
                                    :value="__('Role')" />
                                <select id="role"
                                    name="role"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Roles</option>
                                    <option value="admin"
                                        {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="staff"
                                        {{ $role === 'staff' ? 'selected' : '' }}>Staff</option>
                                    <option value="user"
                                        {{ $role === 'user' ? 'selected' : '' }}>User</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <x-input-label for="status"
                                    :value="__('Status')" />
                                <select id="status"
                                    name="status"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Status</option>
                                    <option value="active"
                                        {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive"
                                        {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="trashed"
                                        {{ $status === 'trashed' ? 'selected' : '' }}>Trashed</option>
                                </select>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-end space-x-2">
                                <x-primary-button type="submit">
                                    {{ __('Filter') }}
                                </x-primary-button>
                                <a href="{{ route('admin.users.index') }}"
                                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                    {{ __('Reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            User</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Role</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Email Verified</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Last Login</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach ($users as $user)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <!-- User Info -->
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full"
                                                            src="{{ $user->profile_picture_url }}"
                                                            alt="{{ $user->name }}">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $user->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ '@' }}{{ $user->username }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $user->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Role -->
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' }}">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>

                                            <!-- Status -->
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @if ($user->trashed())
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                        Deleted
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' }}">
                                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Email Verified -->
                                            <td
                                                class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                @if ($user->email_verified_at)
                                                    <span class="text-green-600 dark:text-green-400">Yes</span>
                                                    <div class="text-xs">
                                                        {{ $user->email_verified_at->format('M j, Y') }}</div>
                                                @else
                                                    <span class="text-red-600 dark:text-red-400">No</span>
                                                @endif
                                            </td>

                                            <!-- Last Login -->
                                            <td
                                                class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    @if ($user->trashed())
                                                        <!-- Restore -->
                                                        <form action="{{ route('admin.users.restore', $user->id) }}"
                                                            method="POST"
                                                            class="inline">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit"
                                                                class="text-green-600 hover:text-green-900 dark:hover:text-green-400"
                                                                onclick="return confirm('Are you sure you want to restore this user?')">
                                                                Restore
                                                            </button>
                                                        </form>

                                                        <!-- Permanent Delete -->
                                                        <form
                                                            action="{{ route('admin.users.force-delete', $user->id) }}"
                                                            method="POST"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                                                                onclick="return confirm('Are you sure you want to permanently delete this user? This action cannot be undone.')">
                                                                Delete Permanently
                                                            </button>
                                                        </form>
                                                    @else
                                                        <!-- Show -->
                                                        <a href="{{ route('admin.users.show', $user) }}"
                                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                                            Show
                                                        </a>
                                                        <!-- Edit -->
                                                        <a href="{{ route('admin.users.edit', $user) }}"
                                                            class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400">
                                                            Edit
                                                        </a>
                                                        <!-- Status Toggle -->
                                                        @if ($user->id !== auth()->id())
                                                            <form action="{{ route('admin.users.status', $user) }}"
                                                                method="POST"
                                                                class="inline">
                                                                @csrf
                                                                @method('POST')
                                                                <button type="submit"
                                                                    class="{{ $user->is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }} dark:hover:text-yellow-400">
                                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <!-- Soft Delete -->
                                                        @if ($user->id !== auth()->id())
                                                            <form action="{{ route('admin.users.destroy', $user) }}"
                                                                method="POST"
                                                                class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No users found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $search || $role || $status ? 'Try adjusting your search filters.' : 'Get started by creating a new user.' }}
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('admin.users.create') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Add New User
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
