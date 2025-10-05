<x-app-layout 
    title="Dashboard" 
    :pageTitle="__('Dashboard')"
    :sidebarCollapsed="session('sidebar_collapsed', false)"
>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stats-card 
            title="Total Users" 
            value="1,248" 
            icon="users" 
            color="blue" 
        />
        
        <x-stats-card 
            title="Revenue" 
            value="$24,580" 
            icon="currency" 
            color="green" 
        />
        
        <x-stats-card 
            title="Orders" 
            value="356" 
            icon="shopping-bag" 
            color="purple" 
        />
        
        <x-stats-card 
            title="Growth" 
            value="+12.5%" 
            icon="trending-up" 
            color="red" 
        />
    </div>

    <!-- Charts and Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-recent-activity />
        <x-quick-stats />
    </div>

</x-app-layout>
