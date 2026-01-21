<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    <!-- Header Info -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ $program->name }}
            </h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm">
                {{ $program->major->department->name }} • {{ $program->degree->name }}
            </p>
        </div>
        <button wire:click="$set('showAddModal', true)"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
            + Add Course
        </button>
    </div>

    <!-- Roadmap Grid -->
    <div class="space-y-8">
        @for ($y = 1; $y <= 4; $y++)
            {{-- Assuming 4 years standard --}}
            <div class="relative">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-3 border-b pb-2">
                    Year {{ $y }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Term 1 -->
                    @include('livewire.admin.academic.partials.term-card', ['year' => $y, 'term' => 1])

                    <!-- Term 2 -->
                    @include('livewire.admin.academic.partials.term-card', ['year' => $y, 'term' => 2])
                </div>
            </div>
        @endfor
    </div>

    <!-- Add Modal -->
    @if ($showAddModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500 bg-opacity-75">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                <h3 class="text-lg font-bold mb-4 dark:text-white">
                    Add Course to Curriculum
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm dark:text-gray-300">
                            Course
                        </label>
                        {{-- <select wire:model="course_id"
                            class="w-full border rounded p-2 dark:bg-gray-900 dark:text-white">
                            <option value="">Select...</option>
                            @foreach ($allCourses as $c)
                                <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}
                                    ({{ $c->credits }} Cr)
                                </option>
                            @endforeach
                        </select> --}}
                        <x-autocomplete-select :items="$allCourses"
                            wire-model="course_id"
                            placeholder="Type course code or name..." />
                        @error('course_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm dark:text-gray-300">
                                Year
                            </label>
                            <select type="number"
                            @foreach ([1, 2, 3, 4] as $y)
                                wire:model="year"
                                class="w-full border rounded p-2 dark:bg-gray-900 dark:text-white">
                                <option value="{{ $y }}">Year {{ $y }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm dark:text-gray-300">
                                Term
                            </label>
                            <select type="number"
                                @foreach ([1, 2] as $t)
                                wire:model="term"
                                class="w-full border rounded p-2 dark:bg-gray-900 dark:text-white">
                                <option value="{{ $t }}">Term {{ $t }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="$set('showAddModal', false)"
                        class="text-gray-500">
                        Cancel
                    </button>
                    <button wire:click="addCourse"
                        class="bg-indigo-600 text-white px-4 py-2 rounded">
                        Add
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

