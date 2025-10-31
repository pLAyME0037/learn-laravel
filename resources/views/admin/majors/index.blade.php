<x-app-layout title="Majors"
    :pageTitle="__('Majors')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Manage Majors</h3>
                        <a href="{{ route('admin.majors.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add New Major') }}
                        </a>
                    </div>

                    <div class="mb-4">
                        <input type="text"
                            placeholder="Search majors..."
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </div>

                    @php
                        $headers = [
                            'major_name' => 'Major Name',
                            'department_name' => 'Department',
                            'degree_name' => 'Degree',
                            'major_cost' => 'Cost',
                            'payment_frequency' => 'Payment Frequency',
                        ];

                        $data = $majors->map(function ($major) {
                            return [
                                'major_name' => $major->major_name,
                                'department_name' => $major->department->name ?? 'N/A',
                                'degree_name' => $major->degree->name ?? 'N/A',
                                'major_cost' => $major->formatted_major_cost,
                                'payment_frequency' => $major->payment_frequency_description,
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.majors.show',
                                'label' => 'View',
                                'params' => ['major' => 'id'],
                                'class' => 'text-blue-600 hover:text-blue-900',
                            ],
                            'edit' => [
                                'route' => 'admin.majors.edit',
                                'label' => 'Edit',
                                'params' => ['major' => 'id'],
                                'class' => 'text-indigo-600 hover:text-indigo-900',
                            ],
                            'delete' => [
                                'route' => 'admin.majors.destroy',
                                'label' => 'Delete',
                                'params' => ['major' => 'id'],
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
                        {{ $majors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
