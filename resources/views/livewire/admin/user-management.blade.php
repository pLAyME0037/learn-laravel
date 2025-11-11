<div>
    <div class="p-4">
        <input wire:model.live="search"
            type="text"
            placeholder="Search users..."
            class="p-2 border rounded">
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg"
            role="alert">
            {{ session('message') }}
        </div>
    @endif

    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $user->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @foreach ($user->roles as $role)
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $role->name }}
                                <button wire:click="removeRole({{ $user->id }}, '{{ $role->name }}')"
                                    class="ml-1 text-red-500 hover:text-red-700">x</button>
                            </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <select wire:change="assignRole({{ $user->id }}, $event.target.value)">
                            <option>Assign Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="p-4">
        {{ $users->links() }}
    </div>
</div>
