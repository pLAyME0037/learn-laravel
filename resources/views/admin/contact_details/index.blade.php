<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Manage Contact Details
                        </h3>
                        <a href="{{ route('admin.contact-details.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add New Contact Detail') }}
                        </a>
                    </div>

                    <div class="mb-4">
                        <input type="text"
                            placeholder="Search contact details..."
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </div>

                    @php
                        $headers = [
                            'entity_name' => 'Associated Entity',
                            'email' => 'Email',
                            'phone_number' => 'Phone',
                            'address' => 'Address',
                        ];

                        $data = $contactDetails->map(function ($contactDetail) {
                            $entityName = '';
                            if ($contactDetail->user) {
                                $entityName = $contactDetail->user->name . ' (User)';
                            } elseif ($contactDetail->student) {
                                $entityName = $contactDetail->student->user->name . ' (Student)';
                            } elseif ($contactDetail->instructor) {
                                $entityName = $contactDetail->instructor->user->name . ' (Instructor)';
                            }

                            return [
                                'id' => $contactDetail->id, // Add the contact detail ID
                                'entity_name' => $entityName,
                                'email' => $contactDetail->email,
                                'phone_number' => $contactDetail->phone_number,
                                'address' => $contactDetail->address,
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.contact-details.show',
                                'label' => 'View',
                                'params' => ['contact_detail' => 'id'],
                                'class' => 'text-blue-600 hover:text-blue-900',
                            ],
                            'edit' => [
                                'route' => 'admin.contact-details.edit',
                                'label' => 'Edit',
                                'params' => ['contact_detail' => 'id'],
                                'class' => 'text-indigo-600 hover:text-indigo-900',
                            ],
                            'delete' => [
                                'route' => 'admin.contact-details.destroy',
                                'label' => 'Delete',
                                'params' => ['contact_detail' => 'id'],
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
                        {{ $contactDetails->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
