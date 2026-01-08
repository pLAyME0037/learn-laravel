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
 
            <select wire:model.live="filterStatus"
                class="w-1/3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                <option value="">All Status</option>
                <option value="trashed">Trashed</option>
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

    <x-smart-table :config="$instructors" />
</div>
@push('scripts')
    <x-sweet-alert-actions />
@endpush
