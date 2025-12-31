<?php
namespace App\Providers;

use App\Models\Enrollment;
use App\Models\User;
use App\Observers\EnrollmentObserver;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }
        
        // Admin-only directive
        Blade::if('admin', function () {
            $user = auth()->user();
            return $user && ($user->isAdmin() || $user->isSuperUser());
        });

        // Super admin-only directive
        Blade::if('superuser', function () {
            $user = auth()->user();
            return $user && $user->isSuperUser();
        });

        // Staff-only directive (all staff roles)
        Blade::if('staff', function () {
            $user = auth()->user();
            return $user && $user->isStaff();
        });

        // Role-specific directive
        Blade::if('role', function ($role) {
            $user = auth()->user();
            return $user && $user->hasRole($role);
        });

        // Permission-specific directive
        Blade::if('permission', function ($permission) {
            $user = auth()->user();
            return $user && $user?->hasPermission($permission);
        });

        Enrollment::observe(EnrollmentObserver::class);
    }
}
