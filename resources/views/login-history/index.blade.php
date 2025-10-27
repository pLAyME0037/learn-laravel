<?php
use Illuminate\Support\HtmlString;
use Carbon\Carbon;
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Login History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('admin.academic_years.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Academic Year
                        </a>
                    </div>

                    @php
                        $headers = [
                            'user_id' => 'User Name',
                            'ip_address' => 'IP Address',
                            'Sec_Ch_Ua' => 'Browser',
                            'Sec_Ch_Ua_Platform' => 'OS Platform',
                            'user_agent' => 'User Agent',
                            'login_at' => 'Login At',
                        ];

                        $data = $loginHistories->map(function ($history) {
                            return [
                                'user_id' => $history->user->name,
                                'ip_address' => $history->ip_address,
                                'Sec_Ch_Ua' => new HtmlString(nl2br(e(wordwrap($history->Sec_Ch_Ua, 30, "\n", true)))),
                                'Sec_Ch_Ua_Platform' => $history->Sec_Ch_Ua_Platform,
                                'user_agent' => new HtmlString(
                                    nl2br(e(wordwrap($history->user_agent, 50, "\n", true))),
                                ),
                                'login_at' => $history->login_at
                                    ? Carbon::parse($history->login_at)->format('d-m-Y H:i:s')
                                    : '',
                            ];
                        });

                        $actions = [
                            // Define any actions if needed
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
                        {{ $loginHistories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
