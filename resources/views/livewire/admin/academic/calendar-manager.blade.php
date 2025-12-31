<div class="space-y-6 py-6">

    <!-- Top Action Bar -->
    <div class="flex justify-end">
        <button wire:click="createYear"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow flex items-center">
            <svg class="w-5 h-5 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            New Academic Year
        </button>
    </div>

    <!-- List Years -->
    @foreach ($years as $year)
        <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg shadow overflow-hidden mb-6">

            <!-- Year Header -->
            <div
                class="bg-gray-50 dark:bg-gray-700 p-4 flex justify-between items-center border-b dark:border-gray-600">
                <div class="flex items-center gap-4">
                    <h3 class="font-bold text-lg dark:text-white {{ $year->is_current ? 'text-green-600' : '' }}">
                        {{ $year->name }}
                        @if ($year->is_current)
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded ml-2">
                                Current
                            </span>
                        @endif
                    </h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $year->start_date->format('M Y') }} - {{ $year->end_date->format('M Y') }}
                    </span>
                </div>

                <!-- Year Actions -->
                <div class="flex gap-2">
                    <button wire:click="editYear({{ $year->id }})"
                        class="text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                        <svg class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </button>
                    <button wire:click="deleteYear({{ $year->id }})"
                        wire:confirm="Delete {{ $year->name }}? Only possible if no semesters exist."
                        class="text-gray-500 hover:text-red-600">
                        <svg class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Semesters List -->
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-medium text-gray-700 dark:text-gray-300">
                        Semesters
                    </h4>
                    <button wire:click="createSemester({{ $year->id }})"
                        class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-1 rounded border border-indigo-200">
                        + Add Semester
                    </button>
                </div>

                <ul class="space-y-2">
                    @foreach ($year->semesters as $sem)
                        <li
                            class="flex justify-between items-center p-3 rounded-lg {{ $sem->is_active ? 'bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'bg-gray-50 border border-gray-100 dark:bg-gray-900/50 dark:border-gray-700' }}">
                            <div class="flex items-center gap-3">
                                <span class="font-medium dark:text-white">
                                    {{ $sem->name }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $sem->start_date->format('M d') }} - {{ $sem->end_date->format('M d') }}
                                </span>
                                @if ($sem->is_active)
                                    <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded">
                                        ACTIVE
                                    </span>
                                    <button wire:click="confirmCloseSemester"
                                        class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded border border-red-200 font-bold transition-colors">
                                        Close Term & Promote
                                    </button>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                @if (!$sem->is_active)
                                    <button wire:click="toggleActiveSemester({{ $sem->id }})"
                                        class="text-xs bg-white border border-gray-300 hover:bg-gray-50 px-2 py-1 rounded shadow-sm text-gray-600">
                                        Set Active
                                    </button>
                                @endif
                                <button wire:click="editSemester({{ $sem->id }})"
                                    class="text-blue-500 hover:text-blue-700 text-sm">
                                    Edit
                                </button>
                                <button wire:click="deleteSemester({{ $sem->id }})"
                                    wire:confirm="Delete {{ $sem->name }}?"
                                    class="text-red-500 hover:text-red-700 text-sm">
                                    Delete
                                </button>
                            </div>
                        </li>
                    @endforeach
                    @if ($year->semesters->isEmpty())
                        <li class="text-sm text-gray-400 italic text-center py-2">
                            No semesters added yet.
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    @endforeach

    <!-- MODAL for Create/Edit -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    wire:click="$set('showModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                    aria-hidden="true">&#8203;
                </span>

                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                            id="modal-title">
                            {{ $isEditing ? 'Edit' : 'Create' }} {{ ucfirst($editingType) }}
                        </h3>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Name
                                </label>
                                <input type="text"
                                    wire:model="form.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                @error('form.name')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Start Date
                                    </label>
                                    <input type="date"
                                        wire:model="form.start_date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                    @error('form.start_date')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        End Date
                                    </label>
                                    <input type="date"
                                        wire:model="form.end_date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                    @error('form.end_date')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
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
</div>
@push('scripts')
    <x-sweet-alert />
@endpush
