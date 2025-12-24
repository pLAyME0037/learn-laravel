<div class="max-w-full mx-auto sm:px-6 lg:px-8"
    x-data="{ activeTab: @entangle('selectedCategory') }">
    <div class="flex flex-col md:flex-row gap-6">

        <!-- LEFT: Sidebar (Pure Alpine) -->
        <div class="w-full md:w-1/3">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div
                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 font-bold dark:text-white">
                    Categories
                    <button wire:click="createCategory"
                        class="px-4 py-2 gap-4 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500 justify-end">
                        + New Category
                    </button>
                </div>
                <nav class="flex flex-col max-h-[60vh] overflow-y-auto">
                    @foreach ($categories as $cat)
                        <button @click="activeTab = '{{ $cat }}'; $wire.selectCategory('{{ $cat }}')"
                            :class="activeTab === '{{ $cat }}' ?
                                'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 border-l-4 border-l-indigo-500' :
                                'text-gray-700 dark:text-gray-300'"
                            class="text-left px-4 py-3 text-sm font-medium border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            {{ ucwords(str_replace('_', ' ', $cat)) }}
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- RIGHT: Content (Alpine x-show) -->
        <div class="w-full md:w-3/4">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white"
                        x-text="activeTab.replace('_', ' ').toUpperCase() + ' OPTIONS'">
                    </h3>

                    <!-- Create Button needs to know which category is active -->
                    <button wire:click="create"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500">
                        + Add Item
                    </button>
                </div>

                <!-- Loop through ALL Groups, show ONLY active one -->
                @foreach ($categories as $cat)
                    <div x-show="activeTab === '{{ $cat }}'"
                        style="display: none;">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Key</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Label</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($groupedItems[$cat] ?? [] as $item)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-400">
                                                {{ $item->key }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $item->label }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button wire:click="edit({{ $item->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    Edit</button>
                                                <button wire:click="delete({{ $item->id }})"
                                                    wire:confirm="Are you sure?"
                                                    class="text-red-600 hover:text-red-900">
                                                    Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="px-6 py-4 text-center text-sm text-gray-500">
                                                No items found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <!-- Modal (Simple Overlay) -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    wire:click="$set('showModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                    aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                            id="modal-title">
                            {{ $isEditing ? 'Edit Item' : 'Create New Item' }}
                        </h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                <input type="text"
                                    wire:model="category"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600"
                                    placeholder="e.g. gender">
                                @error('category')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Key (Code)
                                </label>
                                <input type="text"
                                    wire:model="key"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600"
                                    placeholder="e.g. M">
                                <p class="text-xs text-gray-500 mt-1">
                                    This is stored in the database.
                                </p>
                                @error('key')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Label (Display)
                                </label>
                                <input type="text"
                                    wire:model="label"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600"
                                    placeholder="e.g. Male">
                                @error('label')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox"
                                    wire:model="is_active"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    Is Active?
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="save"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Save
                        </button>
                        <button wire:click="$set('showModal', false)"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- New category Model --}}
    @if ($showCategoryModal)
        <div class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    wire:click="$set('showCategoryModal', false)">
                </div>

                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Add New Category
                        </h3>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Category Name
                            </label>
                            <input type="text"
                                wire:model="newCategoryName"
                                wire:keydown.enter="saveCategory"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white"
                                placeholder="e.g. payment_method">
                            @error('newCategoryName')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-gray-500 mt-2">
                                Use lowercase and underscores (e.g. marital_status).
                            </p>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="saveCategory"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Create
                        </button>
                        <button wire:click="$set('showCategoryModal', false)"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
