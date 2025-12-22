<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Province -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Province / Capital
        </label>
        <select wire:model.live="province_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
            <option value="">Select Province</option>
            @foreach ($provinces as $prov)
                <option value="{{ $prov->prov_id }}">
                    {{ $prov->name_en }} ({{ $prov->name_kh }})
                </option>
            @endforeach
        </select>
    </div>

    <!-- District -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            District / Khan
        </label>
        <select wire:model.live="district_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600"
            {{ empty($districts) ? 'disabled' : '' }}>
            <option value="">Select District</option>
            @foreach ($districts as $dist)
                <option value="{{ $dist->dist_id }}">
                    {{ $dist->name_en }} ({{ $dist->name_kh }})
                </option>
            @endforeach
        </select>
    </div>

    <!-- Commune -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Commune / Sangkat
        </label>
        <select wire:model.live="commune_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600"
            {{ empty($communes) ? 'disabled' : '' }}>
            <option value="">Select Commune</option>
            @foreach ($communes as $comm)
                <option value="{{ $comm->comm_id }}">
                    {{ $comm->name_en }} ({{ $comm->name_kh }})
                </option>
            @endforeach
        </select>
    </div>

    <!-- Village -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Village
        </label>
        <select wire:model.live="village_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600"
            {{ empty($villages) ? 'disabled' : '' }}>
            <option value="">Select Village</option>
            @foreach ($villages as $vill)
                <option value="{{ $vill->id }}">
                    {{ $vill->name_en }} ({{ $vill->name_kh }})
                </option>
            @endforeach
        </select>
    </div>
</div>
