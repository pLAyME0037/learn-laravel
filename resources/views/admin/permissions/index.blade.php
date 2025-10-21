<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Permission Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <form method="GET"
                            action="{{ route('admin.permissions.index') }}"
                            class="flex items-center space-x-2">
                            <input type="text"
                                name="search"
                                placeholder="Search permissions..."
                                value="{{ $search }}"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <x-primary-button type="submit">Search</x-primary-button>
                        </form>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3">Permission Name</th>
                                    <th scope="col"
                                        class="px-6 py-3">Group</th>
                                    <th scope="col"
                                        class="px-6 py-3">Description</th>
                                    <th scope="col"
                                        class="px-6 py-3">Assigned to Roles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($permissions as $permission)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $permission->name }}
                                        </td>
                                        <td class="px-6 py-4">{{ $permission->group_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $permission->description ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            @forelse ($permission->roles as $role)
                                                <span
                                                    class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $role->name }}</span>
                                            @empty
                                                N/A
                                            @endforelse
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="4"
                                            class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No
                                            permissions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $permissions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
