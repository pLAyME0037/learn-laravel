<div class="py-3 max-w-full mx-auto sm:px-6 lg:px-8">

    {{-- FILTER SECTION --}}
    @php
        $filtersConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Search',
                'placeholder' => 'ID, Name, Email...',
            ],
            [
                'type' => 'select',
                'name' => 'filterDepartment',
                'label' => 'Department',
                'options' => $departments->toArray(),
                'defaultOptionText' => 'All Departments',
            ],
            [
                'type' => 'select',
                'name' => 'filterProgram',
                'label' => 'Program',
                'options' => $programs->toArray(),
                'defaultOptionText' => 'All Programs',
            ],
            [
                'type' => 'select',
                'name' => 'filterStatus',
                'label' => 'Status',
                'options' => [
                    ['value' => 'active', 'text' => 'Active'],
                    ['value' => 'probation', 'text' => 'Probation'],
                    ['value' => 'graduated', 'text' => 'Graduated'],
                    ['value' => 'suspended', 'text' => 'Suspended'],
                    ['value' => 'trashed', 'text' => 'Trash Bin'],
                ],
                'defaultOptionText' => 'All Status',
            ],
        ];
    @endphp

    <x-filter-live :filters="$filtersConfig"
     wire:key="filter-box-{{ $filterDepartment }}"
     uri="admin.students.create"
     createBtn="Add New Student" />

        <x-smart-table :table="$table" />

</div>
@push('scripts')
    <x-sweet-alert-actions />
@endpush
