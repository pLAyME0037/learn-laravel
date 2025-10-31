<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Login Histories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Manage Login Histories
                        </h3>
                        <!-- Search and Filter (Placeholder for now) -->
                        <div class="flex items-center space-x-4">
                            <x-text-input type="text"
                                placeholder="Search..."
                                class="block w-full" />
                            <!-- Add filter dropdowns here if needed -->
                        </div>
                    </div>
                    
                    @php
                        $headers = [
                            'user_name' => 'User Name',
                            'login_at' => 'Login At',
                            'ip_address' => 'IP Address',
                            'platform_and_browser' => 'Platform & Browser',

                            // 'Sec_Ch_Ua' => 'Browser',
                            // 'Sec_Ch_Ua_Platform' => 'OS Platform',
                            // 'user_agent' => 'User Agent',
                        ];
                        $data = $loginHistories->map(function ($loginHistory) {
                            return [
                                'id' => $loginHistory->id,
                                'user_name' => $loginHistory->user->name,
                                'login_at' => $loginHistory->formatted_login_time,
                                'ip_address' => $loginHistory->ip_address,
                                'platform_and_browser' => new Illuminate\Support\HtmlString(nl2br(e(wordwrap($loginHistory->platform_and_browser, 70, "\n", true)))),

                                // 'user_id' => $history->user->name,
                                // 'ip_address' => $history->ip_address,
                                // 'Sec_Ch_Ua' => new HtmlString(nl2br(e(wordwrap($history->Sec_Ch_Ua, 30, "\n", true)))),
                                // 'Sec_Ch_Ua_Platform' => $history->Sec_Ch_Ua_Platform,
                                // 'user_agent' => new HtmlString(
                                //     nl2br(e(wordwrap($history->user_agent, 50, "\n", true))),
                                // ),
                                // 'login_at' => $history->login_at
                                //     ? Carbon::parse($history->login_at)->format('d-m-Y H:i:s')
                                //     : '',
                            ];
                        });

                        $actions = [
                            // 'delete' => [
                            //     'route' => 'admin.login-history.destroy',
                            //     'label' => 'Delete',
                            //     'params' => ['login_history' => 'id'],
                            //     'class' => 'text-red-600 hover:text-red-900',
                            //     'method' => 'DELETE',
                            // ],
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
