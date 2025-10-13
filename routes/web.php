<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
    });
    
    // Admin only routes
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // user management routes
        Route::resource('users', UserController::class)
            ->except(['show']);
        Route::get('users/{user}/show', [UserController::class, 'show'])
            ->name('users.show');
        // User actions - all as POST since they're form submissions
        Route::post('users/{user}/status', [UserController::class, 'updateStatus'])
            ->name('users.status');
        Route::post('users/{user}/restore', [UserController::class, 'restore'])
        ->name('users.restore');
        Route::post('users/{user}/force-delete', [UserController::class, 'forceDelete'])
            ->name('users.force-delete');
    });
        
// Department management routes
Route::middleware(['auth', 'staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function() {
        Route::resource('departments', DepartmentController::class);
        Route::post('/departments/{department}/status', [DepartmentController::class, 'updateStatus'])
            ->name('departments.status');
        Route::post('/departments/{department}/restore', [DepartmentController::class, 'restore'])
            ->name('departments.restore');
    });

Route::middleware(['web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
    Route::post('/toggle-sidebar', [SidebarController::class, 'toggle'])
    ->name('sidebar.toggle');
    Route::post('/theme/set', [ThemeController::class, 'set'])
    ->name('theme.set');
});

Route::middleware(['admin', 'staff'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])
    ->name('admin.users.index');
});

Route::middleware(['staff'])->group(function () {
    Route::get('/staff/users', [UserController::class, 'index'])
    ->name('staff.users.index');
});

// Route::get('/users', [UserController::class, 'index'])
//     ->name('users.index');
require __DIR__ . '/auth.php';
