<div>
    {{-- Controls --}}
    <div class="flex justify-between mb-4">
        <div class="flex gap-4">
            {{-- Search Input --}}
            <x-input-label value="Name" />
            <div>
                <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Search permissions name..."
                   class="bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 rounded-md shadow-sm">
            </div>

            {{-- Group Filter --}}
            <x-input-label value="Groups" />
            <div>
                <select wire:model.live="groupFilter" class="bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 rounded-md shadow-sm">
                    <option value="">All Groups</option>
                    @foreach($groupProperty as $grp)
                        <option value="{{ $grp }}">{{ ucfirst($grp) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="">
            <a href="{{ route('admin.permissions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 dark:text-gray-200 uppercase tracking-wider">
                        Group
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 dark:text-gray-200 uppercase tracking-wider">
                        Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-800 dark:text-gray-200 uppercase tracking-wider">
                        Roles
                    </th>
                    <th class="px-6 py-3 text-gray-800 dark:text-gray-200 text-right">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-900">
                @foreach($permissions as $permission)
                    @php /** @var \Spatie\Permission\Models\Permission $permission */ @endphp
                    <tr class="hover:bg-gray-100 hover:dark:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-400/40 dark:bg-purple-600/40 text-gray-800 dark:text-gray-200">
                                {{ $permission->group }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $permission->name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $permission->description }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @foreach($permission->roles as $role)
                                <span class="bg-blue-100/30 text-blue-800 dark:bg-blue-900/30 dark:text-blue-100 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.permissions.edit', $permission) }}"
                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Edit
                            </a>
                            <button type="button"
                                    wire:click="confDel({{ $permission->id }})"
                                    wire:loading.attr="disabled"
                                    class="text-red-600 hover:text-red-900 font-bold cursor-pointer disabled:opacity-50">
                                <span wire:loading
                                      wire:target="confdel({{ $permission->id }})">
                                      deleting...
                                </span>
                                <span wire:loading.remove
                                      wire:target="confdel({{ $permission->id }})">
                                      delete
                                </span>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4">
            {{ $permissions->links() }}
        </div>
    </div>
</div>

@push('scripts')
    <x-sweet-alert />
@endpush
