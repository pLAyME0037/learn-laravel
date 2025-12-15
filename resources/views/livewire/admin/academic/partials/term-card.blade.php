@php
    // Filter roadmap for this specific Year/Term
    $courses = $roadmap->get($year)?->where('recommended_term', $term) ?? collect();
    $totalCredits = $courses->sum('course.credits');
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow border dark:border-gray-700 flex flex-col h-full">
    <div
        class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
        <span class="font-semibold text-gray-700 dark:text-gray-200">Semester {{ $term }}</span>
        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ $totalCredits }} Credits</span>
    </div>

    <div class="p-4 flex-1 space-y-2">
        @forelse($courses as $item)
            <div
                class="flex justify-between items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded border dark:border-gray-600">
                <div>
                    <div class="font-bold text-sm text-indigo-600 dark:text-indigo-400">{{ $item->course->code }}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-300">{{ $item->course->name }}</div>
                </div>
                <div class="flex items-center space-x-3">
                    <span
                        class="text-xs font-mono bg-gray-100 dark:bg-gray-600 px-1 rounded">{{ $item->course->credits }}</span>
                    <button wire:click="removeCourse({{ $item->id }})"
                        class="text-red-400 hover:text-red-600">
                        &times;
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400 text-sm py-4 italic">No courses</div>
        @endforelse
    </div>
</div>
