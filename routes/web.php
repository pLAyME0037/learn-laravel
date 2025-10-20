<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginHistoryController;
use App\Models\Department;
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
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // department Management
        Route::resource('departments', DepartmentController::class)
            ->except(['show']);
        Route::get('/departments/{department}/show', [
            DepartmentController::class, 'show',
        ])->name('departments.show');
        Route::get('/departments/{department}/restore', [
            DepartmentController::class, 'restore',
        ])->name('departments.restore');
        Route::get('/departments/{department}/force-delete', [
            DepartmentController::class, 'forceDelete',
        ])->name('departments.force-delete');

        // user management routes
        Route::resource('users', UserController::class)
            ->except(['show']);
        Route::get('users/{user}/show', [
            UserController::class, 'show',
        ])->name('users.show');
        // User actions - all as POST since they're form submissions
        Route::post('users/{user}/status', [
            UserController::class, 'updateStatus',
        ])->name('users.status');
        Route::post('users/{user}/restore', [
            UserController::class, 'restore',
        ])->name('users.restore');
        Route::post('users/{user}/force-delete', [
            UserController::class, 'forceDelete',
        ])->name('users.force-delete');

        // Student Management Routes
        Route::resource('students', StudentController::class)
            ->except(['show']);
        Route::get('/students/{student}/show', [
            StudentController::class, 'show'
        ])->name('students.show');
        Route::get('/students/{student}/restore', [
            StudentController::class, 'restore'
        ])->name('students.restore');
        Route::get('/students/{student}/force-delete', [
            StudentController::class, 'forceDelete'
        ])->name('students.force-delete');
        Route::get('/students/{student}/status', [
            StudentController::class, 'updateStatus'
        ])->name('students.status');
        

        // Role & Permission Management
        // Route::resource('roles', RoleController::class);
        // Route::resource('permissions', PermissionController::class);

        // Login History
        Route::get('login-history', [
            LoginHistoryController::class, 'index'
        ])->name('login-history.index');
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

Route::middleware(['staff'])->group(function () {
    Route::get('/staff/users', [UserController::class, 'index'])
        ->name('staff.users.index');
});

// Route::get('/users', [UserController::class, 'index'])
//     ->name('users.index');
require __DIR__ . '/auth.php';
