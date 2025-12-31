<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    <!-- Toolbar -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex gap-4 w-2/3">
            <input wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search instructor..."
                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

            <select wire:model.live="filterDepartment"
                class="w-1/3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                <option value="">All Departments</option>
                @foreach ($departments as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('admin.instructors.create') }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow flex items-center">
            <svg class="w-5 h-5 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4">
                </path>
            </svg>
            New Instructor
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        #
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Name / ID
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Department
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Contact
                    </th>
                    <th
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                {{-- @dd($instructors) --}}
                @forelse($instructors as $instructor)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="pl-6 py-2">
                            <div
                                class="h-6 w-6 rounded-full bg-indigo-100 shadow-md flex items-center justify-center text-indigo-700 font-bold text-sm">
                                {{ $instructors->firstItem() + $loop->index }}
                            </div>
                        </td>
                        <td class="px-0 py-2">
                            <div class="flex items-center">
                                <div class="pl-4">
                                    <x-profile-image size="xs"
                                        src="{{ $instructor->user->profile_picture_url }}"
                                        alt="{{ $instructor->user->username }}" />
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $instructor->user->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $instructor->staff_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                            {{ $instructor->department->name ?? 'Unassigned' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                            <div>{{ $instructor->user->email }}</div>
                            <div class="text-xs text-gray-400">
                                {{ $instructor->contactDetail->phone ?? '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <a href="{{ route('admin.instructors.show', $instructor->id) }}"
                                class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 mr-3">
                                View
                            </a>
                            <a href="{{ route('admin.instructors.edit', $instructor->id) }}"
                                class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400 mr-3">
                                Edit
                            </a>
                            <button wire:click="delete({{ $instructor->id }})"
                                wire:confirm="Are you sure you want to delete this instructor?"
                                class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4"
                            class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No instructors found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t dark:border-gray-700">
            {{ $instructors->links() }}
        </div>
    </div>
</div>
