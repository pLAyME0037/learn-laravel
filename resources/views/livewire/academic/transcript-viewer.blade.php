<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        <!-- Header Actions -->
        <div class="flex justify-end mb-6">
            <button wire:click="downloadPdf"
                wire:loading.attr="disabled"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow flex items-center">
                <svg wire:loading.remove
                    class="w-5 h-5 mr-2"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span wire:loading.remove>Download PDF</span>
                <span wire:loading>Generating...</span>
            </button>
        </div>

        <!-- Transcript Container -->
        <div
            class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">

            <!-- Student Header -->
            <div class="bg-gray-50 dark:bg-gray-700 p-8 border-b dark:border-gray-600">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                            Official Transcript
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Generated on {{ now()->format('F d, Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 font-khmer">
                            {{ $student->user->name }}
                        </h2>
                        <p class="text-indigo-600 dark:text-indigo-400 font-mono">
                            {{ $student->student_id }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $student->program->name }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex gap-8 text-sm">
                    <div>
                        <span class="block text-gray-400 uppercase text-xs">
                            CGPA
                        </span>
                        <span class="font-bold text-xl dark:text-white">
                            {{ $student->cgpa }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-gray-400 uppercase text-xs">
                            Total Credits
                        </span>
                        <span class="font-bold text-xl dark:text-white">
                            {{ $student->total_credits_earned }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Semester Loop -->
            <div class="p-8 space-y-8">
                @forelse($transcriptData as $semesterName => $records)
                    <div>
                        <h3
                            class="text-lg font-bold text-gray-700 dark:text-gray-300 border-b-2 border-indigo-500 inline-block mb-3">
                            {{ $semesterName }}
                        </h3>
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 dark:bg-gray-900 text-gray-500 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-2">Code</th>
                                    <th class="px-4 py-2">Course Title</th>
                                    <th class="px-4 py-2 text-center">Credits</th>
                                    <th class="px-4 py-2 text-center">Grade</th>
                                    <th class="px-4 py-2 text-center">Points</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($records as $row)
                                    <tr class="dark:text-gray-300">
                                        <td class="px-4 py-2 font-mono text-xs">
                                          {{ $row->classSession->course->code }}
                                        </td>
                                        <td class="px-4 py-2">
                                          {{ $row->classSession->course->name }}
                                       </td>
                                        <td class="px-4 py-2 text-center">
                                          {{ $row->classSession->course->credits }}
                                       </td>
                                        <td
                                            class="px-4 py-2 text-center font-bold {{ $row->final_grade < 50 ? 'text-red-500' : 'text-green-600' }}">
                                            {{ $row->grade_letter }}
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                          {{ $row->grade_points }}
                                       </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-400 italic">
                        No completed academic records found.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>
