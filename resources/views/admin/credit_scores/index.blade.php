<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Credit Scores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Manage Credit Scores</h3>
                        <a href="{{ route('admin.credit-scores.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add New Credit Score') }}
                        </a>
                    </div>

                    <div class="mb-4">
                        <input type="text"
                            placeholder="Search credit scores..."
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </div>

                    @php
                        $headers = [
                            'student_name' => 'Student',
                            'course_name' => 'Course',
                            'score' => 'Score',
                            'status' => 'Status',
                        ];

                        $data = $creditScores->map(function ($creditScore) {
                            return [
                                'student_name' => $creditScore->student->user->name,
                                'course_name' => $creditScore->course->name,
                                'score' => $creditScore->score,
                                'status' => $creditScore->score_status,
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.credit_scores.show',
                                'label' => 'View',
                                'params' => ['credit_score' => 'id'],
                                'class' => 'text-blue-600 hover:text-blue-900',
                            ],
                            'edit' => [
                                'route' => 'admin.credit_scores.edit',
                                'label' => 'Edit',
                                'params' => ['credit_score' => 'id'],
                                'class' => 'text-indigo-600 hover:text-indigo-900',
                            ],
                            'delete' => [
                                'route' => 'admin.credit_scores.destroy',
                                'label' => 'Delete',
                                'params' => ['credit_score' => 'id'],
                                'class' => 'text-red-600 hover:text-red-900',
                                'method' => 'DELETE',
                            ],
                        ];
                    @endphp
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <x-dynamic-table :headers="$headers"
                        :data="$data"
                        :actions="$actions"
                        :options="['wrapperClass' => 'border border-gray-200']" />

                    <div class="mt-4">
                        {{ $creditScores->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
