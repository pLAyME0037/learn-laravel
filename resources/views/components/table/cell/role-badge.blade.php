@props(['role', 'user'])

@php
    $roleColorClass = match ($role->name) {
        'Super Administrator' 
            => 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100 -bottom-1 -right-1 hover:bg-purple-200 hover:ring-2 hover:ring-purple-500 shadow-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800',
        'admin' 
            => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100 -bottom-1 -right-1 hover:bg-indigo-200 hover:ring-2 hover:ring-indigo-500 shadow-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800',
        'hod' 
            => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 -bottom-1 -right-1 hover:bg-green-200 hover:ring-2 hover:ring-green-500 shadow-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800',
        'register' 
            => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100 -bottom-1 -right-1 hover:bg-yellow-200 hover:ring-2 hover:ring-yellow-500 shadow-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800',
        'staff' 
            => 'bg-teal-100 text-teal-800 dark:bg-teal-800 dark:text-teal-100 -bottom-1 -right-1 hover:bg-teal-200 hover:ring-2 hover:ring-teal-500 shadow-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800',
        'professor' 
            => 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100 -bottom-1 -right-1 hover:bg-orange-200 hover:ring-2 hover:ring-orange-500 shadow-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800',
        'student'
            => 'bg-blue-100 text-gray-800 dark:bg-blue-700 dark:text-gray-300 -bottom-1 -right-1 hover:bg-blue-200 hover:ring-2 hover:ring-blue-500 shadow-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800',
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    };
@endphp

<span @class([
    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
    $roleColorClass,
])>
    <a href="{{ route('admin.users.edit-access', ['user' => $user->id, 'role' => $role->id]) }}">
        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
    </a>
</span>
