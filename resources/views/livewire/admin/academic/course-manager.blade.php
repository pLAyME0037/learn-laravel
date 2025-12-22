<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Toolbar -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-4 w-2/3">
                <input wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search code or name..."
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">

                <select wire:model.live="filterDepartment"
                    class="w-1/3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                    <option value="">All Departments</option>
                    @foreach ($departments as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <button wire:click="create"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow flex items-center">
                <span class="mr-2 text-lg">+</span> Add Course
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            No</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Code</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Name</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Credits</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Department</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                {{ $courses->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4 font-mono text-sm text-indigo-600 dark:text-indigo-400 font-bold">
                                {{ $course->code }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $course->name }}
                                @if ($course->description)
                                    <p class="text-xs text-gray-500 truncate w-64">
                                        {{ $course->description }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $course->credits }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $course->department->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <button wire:click="edit({{ $course->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 mr-3">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $course->id }})"
                                    wire:confirm="Delete this course?"
                                    class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No courses found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t dark:border-gray-700">
                {{ $courses->links() }}
            </div>
        </div>

        <!-- Modal -->
        @if ($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                        wire:click="$set('showModal', false)">
                    </div>
                    <div
                        class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                                {{ $isEditing ? 'Edit Course' : 'Create Course' }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Code -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Course
                                        Code
                                    </label>
                                    <input type="text"
                                        wire:model="code"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600"
                                        placeholder="e.g. CS101">
                                    @error('code')
                                        <span class="text-red-500 text-xs">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <!-- Credits -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Credits
                                    </label>
                                    <input type="number"
                                        wire:model="credits"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                    @error('credits')
                                        <span class="text-red-500 text-xs">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <!-- Name -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Course Name
                                    </label>
                                    <input type="text"
                                        wire:model="name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                    @error('name')
                                        <span class="text-red-500 text-xs">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <!-- Department -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Department
                                    </label>
                                    <select wire:model="department_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $id => $deptName)
                                            <option value="{{ $id }}">
                                                {{ $deptName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <span class="text-red-500 text-xs">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <!-- Prerequisites (Multi-select) -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Prerequisites
                                    </label>
                                    <select wire:model="prerequisites"
                                        multiple
                                        class="mt-1 block w-full h-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                        @foreach ($allCourses as $c)
                                            <option value="{{ $c->id }}">{{ $c->code }} -
                                                {{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Hold Ctrl (Windows) or Cmd (Mac) to select multiple.
                                    </p>
                                </div>
                                <!-- Description -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Description
                                    </label>
                                    <textarea wire:model="description"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600"></textarea>
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
</div>
