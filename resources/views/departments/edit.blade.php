<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Department') }}
            </h2>
            <a href="{{ route('admin.departments.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Back to Departments') }}
            </a>
        </div>
    </x-slot>

    <div class="py-9">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.departments.update', $department) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Current Department Info -->
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $department->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $department->code }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Created: {{ $department->created_at->format('M j, Y') }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                    {{ $department->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <!-- Basic Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Name -->
                                    <div>
                                        <x-input-label for="name" :value="__('Department Name')" />
                                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                                      :value="old('name', $department->name)" required autofocus />
                                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                    </div>

                                    <!-- Code -->
                                    <div>
                                        <x-input-label for="code" :value="__('Department Code')" />
                                        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" 
                                                      :value="old('code', $department->code)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('code')" />
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mt-4">
                                    <x-input-label for="description" :value="__('Description')" />
                                    <textarea id="description" name="description" rows="3"
                                              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                              placeholder="{{ __('Brief description of the department...') }}">{{ old('description', $department->description) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                </div>
                            </div>

                            <!-- Head of Department -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Head of Department</h3>
                                
                                <div>
                                    <x-input-label for="hod_id" :value="__('Select HOD')" />
                                    <select id="hod_id" name="hod_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">-- No HOD Selected --</option>
                                        @foreach($hods as $hod)
                                            <option value="{{ $hod->id }}" {{ old('hod_id', $department->hod_id) == $hod->id ? 'selected' : '' }}>
                                                {{ $hod->name }} ({{ $hod->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('hod_id')" />
                                    @if($department->hod)
                                        <p class="mt-1 text-sm text-green-600 dark:text-green-400">
                                            Current HOD: {{ $department->hod->name }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Contact Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Email -->
                                    <div>
                                        <x-input-label for="email" :value="__('Email Address')" />
                                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                                                      :value="old('email', $department->email)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                    </div>

                                    <!-- Phone -->
                                    <div>
                                        <x-input-label for="phone" :value="__('Phone Number')" />
                                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" 
                                                      :value="old('phone', $department->phone)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                    </div>
                                </div>

                                <!-- Office Location -->
                                <div class="mt-4">
                                    <x-input-label for="office_location" :value="__('Office Location')" />
                                    <x-text-input id="office_location" name="office_location" type="text" class="mt-1 block w-full" 
                                                  :value="old('office_location', $department->office_location)" placeholder="Building, Room Number" />
                                    <x-input-error class="mt-2" :messages="$errors->get('office_location')" />
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Established Year -->
                                    <div>
                                        <x-input-label for="established_year" :value="__('Established Year')" />
                                        <x-text-input id="established_year" name="established_year" type="number" 
                                                      class="mt-1 block w-full" :value="old('established_year', $department->established_year)" 
                                                      min="1900" :max="date('Y')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('established_year')" />
                                    </div>

                                    <!-- Budget -->
                                    <div>
                                        <x-input-label for="budget" :value="__('Annual Budget')" />
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                            </div>
                                            <x-text-input id="budget" name="budget" type="number" step="0.01"
                                                          class="block w-full pl-7 pr-12" 
                                                          :value="old('budget', $department->budget)" placeholder="0.00" />
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('budget')" />
                                    </div>
                                </div>

                                <!-- Active Status -->
                                <div class="mt-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_active" value="1" 
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800"
                                               {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('Department is active and can accept students') }}
                                        </span>
                                    </label>
                                    <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t dark:border-gray-700">
                                <a href="{{ route('admin.departments.index') }}" 
                                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                                    {{ __('Cancel') }}
                                </a>
                                <x-primary-button>
                                    {{ __('Update Department') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>

                    <!-- Danger Zone -->
                    @can('departments.delete')
                        <div class="mt-8 border-t border-red-200 dark:border-red-800 pt-6">
                            <h3 class="text-lg font-medium text-red-600 dark:text-red-400">Danger Zone</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Permanently delete this department. This action cannot be undone.') }}
                            </p>
                            
                            @if($department->canDelete())
                                <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" class="mt-4">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button 
                                        onclick="return confirm('Are you sure you want to delete this department? This will remove all associated data permanently.')">
                                        {{ __('Delete Department') }}
                                    </x-danger-button>
                                </form>
                            @else
                                <p class="mt-2 text-sm text-yellow-600 dark:text-yellow-400">
                                    {{ __('This department cannot be deleted because it has associated users or programs.') }}
                                </p>
                            @endif
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>