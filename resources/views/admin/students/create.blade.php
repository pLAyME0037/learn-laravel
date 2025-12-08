<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __("Create New Student") }}
            </h2>
            <a href="{{ route('admin.students.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __("Back to Students") }}
            </a>
        </div>
    </x-slot>

    <livewire:admin.students.create-student />
</x-app-layout>
