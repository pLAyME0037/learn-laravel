<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Permissions for Role: ') . $role->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST"
                        action="{{ route('admin.roles.update-permissions', $role) }}">
                        @csrf
                        @method('PUT')

                        <h3 class="text-lg font-semibold mb-4">Assign Permissions</h3>

                        @foreach ($permissions as $groupName => $groupPermissions)
                            <div class="mb-6 p-4 border rounded-lg dark:border-gray-700">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200">{{ ucfirst($groupName) }}
                                    </h4>
                                    <div>
                                        <button type="button"
                                            class="text-blue-600 hover:underline text-sm mr-2"
                                            onclick="selectAllPermissions('{{ $groupName }}')">Select All</button>
                                        <button type="button"
                                            class="text-red-600 hover:underline text-sm"
                                            onclick="deselectAllPermissions('{{ $groupName }}')">Deselect
                                            All</button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($groupPermissions as $permission)
                                        <div class="flex items-center">
                                            <input type="checkbox"
                                                name="permissions[]"
                                                value="{{ $permission->name }}"
                                                id="permission-{{ $permission->id }}"
                                                class="form-checkbox h-5 w-5 text-indigo-600 transition duration-150 ease-in-out {{ $groupName }}-permission"
                                                {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                            <label for="permission-{{ $permission->id }}"
                                                class="ml-2 text-gray-700 dark:text-gray-300">{{ $permission->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Update Permissions') }}
                            </x-primary-button>
                        </div>
                    </form>

                    {{-- Extract/Copy Permissions From... Button --}}
                    <div class="mt-8 border-t dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold mb-4">Copy Permissions</h3>
                        <button type="button"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            onclick="Livewire.dispatch('openModal', { component: 'admin.roles.copy-permissions-modal', arguments: { roleId: {{ $role->id }} } })">
                            Extract/Copy Permissions From...
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function selectAllPermissions(groupName) {
                document.querySelectorAll(`.${groupName}-permission`).forEach(checkbox => {
                    checkbox.checked = true;
                });
            }

            function deselectAllPermissions(groupName) {
                document.querySelectorAll(`.${groupName}-permission`).forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
        </script>
    @endpush
</x-app-layout>
