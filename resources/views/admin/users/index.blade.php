<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User Management') }}
            </h2>
            @can('create', App\Models\User::class)
                <a href="{{ route('admin.users.create') }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Add New User') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $headers = ['User', 'Role', 'Status', 'Email Verified', 'User Status', 'Actions'];

                // Prepare filter definitions directly in the view for clarity
                $roleOptions = $roles
                    ->map(fn($name) => ['value' => $name, 'text' => ucfirst($name)])
                    ->prepend(['value' => 'no_roles', 'text' => 'No Roles'])
                    ->toArray();

                $filterDefinitions = [
                    [
                        'type' => 'text',
                        'name' => 'search',
                        'label' => 'Search',
                        'value' => $filters['search'] ?? '',
                        'placeholder' => 'Name, email, or username',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'role',
                        'label' => 'Role',
                        'options' => $roleOptions,
                        'selectedValue' => $filters['role'] ?? '',
                        'defaultOptionText' => 'All Roles',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'status',
                        'label' => 'Status',
                        'options' => [
                            ['value' => 'active', 'text' => 'Active'],
                            ['value' => 'inactive', 'text' => 'Inactive'],
                            ['value' => 'trashed', 'text' => 'Trashed'],
                        ],
                        'selectedValue' => $filters['status'] ?? '',
                        'defaultOptionText' => 'All Status',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'orderby',
                        'label' => 'Order By',
                        'options' => [
                            ['value' => 'a_to_z', 'text' => 'a-z'],
                            ['value' => 'z_to_a', 'text' => 'z-a'],
                            ['value' => 'newest', 'text' => 'Newest'],
                            ['value' => 'oldest', 'text' => 'Oldest'],
                        ],
                        'selectedValue' => $filters['orderby'] ?? 'newest',
                    ],
                ];
            @endphp

            <x-filter-box :action="route('admin.users.index')"
                :filters="$filterDefinitions"
                createBtn="Add New User"
                uri="admin.users.create" />


            <x-table :headers="$headers"
                :data="$users"
                :options="['wrapperClass' => 'mt-6']">

                <x-slot name="bodyContent">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{-- Users --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-table.cell.user-info :user="$user" />
                            </td>
                            {{-- Roles --}}
                            <td class="px-6 py-4 whitespace-nowrap space-y-1">
                                @forelse ($user->roles as $role)
                                    <x-table.cell.role-badge :role="$role"
                                        :user="$user" />
                                @empty
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 -bottom-1 -right-1 hover:bg-gray-200 hover:ring-2 hover:ring-gray-500 shadow-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                        <a href="{{ route('admin.users.edit-access', $user->id) }}">
                                            No Roles
                                        </a>
                                    </span>
                                @endforelse
                            </td>
                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-table.cell.status-badge :user="$user" />
                            </td>
                            {{-- Email Verified --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if ($user->email_verified_at)
                                    <span class="text-green-600 dark:text-green-400">
                                        Yes
                                    </span>
                                    <div class="text-xs">
                                        {{ $user->email_verified_at->format('M j, Y') }}
                                    </div>
                                @else
                                    <span class="text-red-600 dark:text-red-400">
                                        No
                                    </span>
                                @endif
                            </td>
                            {{-- User Status --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <x-table.cell.user-state :user="$user" />
                            </td>
                            {{-- Action --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if ($user->trashed())
                                        @can('restore', $user)
                                            <x-table.action :action="[
                                                'type' => 'post-button',
                                                'label' => 'Restore',
                                                'route' => 'admin.users.restore',
                                                'params' => ['user' => 'id'],
                                                'class' => 'text-green-600',
                                            ]"
                                                :row="$user" />
                                        @endcan
                                        @can('forceDelete', $user)
                                            <x-table.action :action="[
                                                'type' => 'delete',
                                                'label' => 'Delete',
                                                'route' => 'admin.users.force-delete',
                                                'params' => ['user' => 'id'],
                                            ]"
                                                :row="$user" />
                                        @endcan
                                    @else
                                        @can('view', $user)
                                            <x-table.action :action="[
                                                'type' => 'link',
                                                'label' => 'Show',
                                                'route' => 'admin.users.show',
                                                'params' => ['user' => 'id'],
                                                'class' => 'text-gray-500',
                                            ]"
                                                :row="$user" />
                                        @endcan
                                        @can('changePassword', $user)
                                            <x-table.action :action="[
                                                'type' => 'link',
                                                'label' => 'Edit',
                                                'route' => 'admin.users.edit',
                                                'params' => ['user' => 'id'],
                                            ]"
                                                :row="$user" />
                                        @endcan
                                        @can('updateStatus', $user)
                                            <x-table.action :action="[
                                                'type' => 'link',
                                                'label' => 'Accessibility',
                                                'route' => 'admin.users.edit-access',
                                                'params' => ['user' => 'id'],
                                                'class' => 'text-yellow-600',
                                            ]"
                                                :row="$user" />
                                            <x-table.action :action="[
                                                'type' => 'post-button',
                                                'label' => $user->is_active ? 'Disactivate' : 'Activate',
                                                'route' => 'admin.users.status',
                                                'params' => ['user' => 'id'],
                                                'class' => $user->is_active
                                                    ? 'text-yellow-600 hover:text-yellow-500'
                                                    : 'text-green-600 hover:text-green-500',
                                            ]"
                                                :row="$user" />
                                        @endcan
                                        @can('delete', $user)
                                            <x-table.action :action="[
                                                'type' => 'delete',
                                                'label' => 'Trash',
                                                'route' => 'admin.users.destroy',
                                                'params' => ['user' => 'id'],
                                            ]"
                                                :row="$user" />
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}"
                                class="px-6 py-4 text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    No users found.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </x-slot>

                <x-slot name="pagination">
                    {{ $users->links() }}
                </x-slot>

                <x-slot name="empty">
                    <div class="text-center py-8">
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                            No users found
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if (array_filter($filters))
                                Try adjusting your search filters.
                            @else
                                Get started by creating a new user.
                            @endif
                        </p>
                    </div>
                </x-slot>
            </x-table>
        </div>
    </div>
</x-app-layout>
