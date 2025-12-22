<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    <!-- Header & Search -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">
            User Management
        </h2>

        <div class="relative w-80">
            <input wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search users..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div
        class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        User</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Roles</th>
                    <th
                        class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Manage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">

                        <!-- User Info -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <x-profile-image size="xs"
                                        src="{{ $user->profile_picture_url }}"
                                        alt="" />
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $user->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $user->email }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Roles List -->
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($user->roles as $role)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200 dark:border-blue-800">
                                        {{ $role->name }}
                                        <button wire:click="removeRole({{ $user->id }}, '{{ $role->name }}')"
                                            wire:confirm="Remove role '{{ $role->name }}' from {{ $user->name }}?"
                                            class="ml-1.5 text-blue-400 hover:text-blue-600 dark:hover:text-white focus:outline-none">
                                            <svg class="h-3 w-3"
                                                fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                @endforeach
                                @if ($user->roles->isEmpty())
                                    <span class="text-xs text-gray-400 italic">No roles assigned</span>
                                @endif
                            </div>
                        </td>

                        <!-- Assign Role Dropdown -->
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="relative inline-block text-left"
                                x-data="{ open: false }">
                                <button @click="open = !open"
                                    @click.away="open = false"
                                    type="button"
                                    class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-3 py-1.5 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                                    Assign Role
                                    <svg class="-mr-1 ml-2 h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div x-show="open"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50 focus:outline-none"
                                    style="display: none;">
                                    <div class="py-1">
                                        @foreach ($roles as $role)
                                            <button
                                                wire:click="assignRole({{ $user->id }}, '{{ $role->name }}'); open = false;"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white">
                                                {{ $role->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3"
                            class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            No users found matching "{{ $search }}".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
