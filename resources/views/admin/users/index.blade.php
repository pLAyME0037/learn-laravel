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

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @php
            // 1. FILTERS
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
                        ['value' => 'a_to_z', 'text' => 'A-Z'],
                        ['value' => 'z_to_a', 'text' => 'Z-A'],
                        ['value' => 'newest', 'text' => 'Newest'],
                        ['value' => 'oldest', 'text' => 'Oldest'],
                    ],
                    'selectedValue' => $filters['orderby'] ?? 'newest',
                ],
            ];

            // 2. COLUMNS
            $columns = [
                [
                    'key' => 'user_info',
                    'label' => 'User Infrmation',
                    'align' => 'center',
                ],
                [
                    'key' => 'roles',
                    'label' => 'Role',
                    'align' => 'center',
                ],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'align' => 'center',
                ],
                [
                    'key' => 'email_verified_at',
                    'label' => 'Email Verified',
                    'align' => 'center',
                ],
                [
                    'key' => 'user_status',
                    'label' => 'User Status',
                    'align' => 'center',
                ],
            ];
        @endphp

        <x-filter-box :action="route('admin.users.index')"
            :filters="$filterDefinitions"
            uri="admin.users.create"
            createBtn="Add New User" />

        {{-- HYBRID DATA TABLE --}}
        <x-data-table :rows="$users"
            :columns="$columns"
            :selectable="true"
            :with-actions="true"
            :sort-col="request('orderby')"
            :sort-dir="request('direction', 'asc')">

            <x-slot name="body">
                @forelse ($users as $user)
                    <x-table.row wire:key="row-{{ $user->id }}"
                        :item-id="$user->id">

                        {{-- CHECKBOX --}}
                        <x-table.cell class="w-4">
                            <div
                                class="h-6 w-6 rounded-full bg-indigo-100 shadow-md flex items-center justify-center text-indigo-700 font-bold text-sm">
                                {{ $users->firstItem() + $loop->index }}
                            </div>
                        </x-table.cell>

                        {{-- USER INFO --}}
                        <x-table.cell>
                            <x-table.cell.user-info :user="$user" />
                        </x-table.cell>

                        {{-- ROLES --}}
                        <x-table.cell>
                            <div class="space-y-1">
                                @forelse ($user->roles as $role)
                                    <x-table.cell.role-badge :role="$role"
                                        :user="$user" />
                                @empty
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        <a href="{{ route('admin.users.edit-access', $user->id) }}">
                                            No Roles
                                        </a>
                                    </span>
                                @endforelse
                            </div>
                        </x-table.cell>

                        {{-- STATUS --}}
                        <x-table.cell>
                            <x-table.cell.status-badge :user="$user" />
                        </x-table.cell>

                        {{-- VERIFIED --}}
                        <x-table.cell>
                            @if ($user->email_verified_at)
                                <div class="flex flex-col">
                                    <span class="text-green-600 dark:text-green-400 font-bold text-xs">
                                        Verified
                                    </span>
                                    <span class="text-[10px] text-gray-500">
                                        {{ $user->email_verified_at->format('M j, Y') }}
                                    </span>
                                </div>
                            @else
                                <span class="text-red-600 dark:text-red-400 font-bold text-xs">
                                    Unverified
                                </span>
                            @endif
                        </x-table.cell>

                        {{-- USER STATE --}}
                        <x-table.cell>
                            <x-table.cell.user-state :user="$user" />
                        </x-table.cell>

                        {{-- ACTIONS --}}
                        <x-table.cell class="text-right">
                            <div class="flex items-center justify-end space-x-2">
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
                                    @can('changePassword', $user)
                                        <x-table.action :action="[
                                            'type' => 'link',
                                            'label' => '',
                                            'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\' stroke-width=\'2\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z\' /></svg>',
                                            'route' => 'admin.users.edit',
                                            'params' => ['user' => 'id'],
                                        ]"
                                            :row="$user" />
                                    @endcan
                                    @can('updateStatus', $user)
                                        <x-table.action :action="[
                                            'type' => 'post-button',
                                            'label' => $user->is_active ? 'Deactivate' : 'Activate',
                                            'route' => 'admin.users.status',
                                            'params' => ['user' => 'id'],
                                            'class' => $user->is_active
                                                ? 'text-amber-600 hover:text-amber-500'
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
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 2 }}"
                            class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                                <span class="text-lg font-medium">
                                    No users found
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-slot>
        </x-data-table>
    </div>
</x-app-layout>
