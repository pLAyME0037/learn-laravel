<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SystemConfigController;
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
        Route::get('users/{user}/edit-access', [
            UserController::class, 'editAccess',
        ])->name('users.edit-access');
        Route::put('users/{user}/update-access', [
            UserController::class, 'updateAccess',
        ])->name('users.update-access');

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
            StudentController::class, 'show',
        ])->name('students.show');
        Route::get('/students/{student}/restore', [
            StudentController::class, 'restore',
        ])->name('students.restore');
        Route::get('/students/{student}/force-delete', [
            StudentController::class, 'forceDelete',
        ])->name('students.force-delete');
        Route::get('/students/{student}/status', [
            StudentController::class, 'updateStatus',
        ])->name('students.status');

        // Login History
        Route::get('login-history', [
            LoginHistoryController::class, 'index',
        ])->name('login-history.index');

        // System Configuration
        Route::get('system-configs', [
            SystemConfigController::class, 'index',
        ])->name('system-configs.index');
        Route::post('system-configs', [
            SystemConfigController::class, 'update',
        ])->name('system-configs.update');

        // Academic Year Management
        Route::resource('academic_years', AcademicYearController::class);

        // Semester Management
        Route::resource('semesters', SemesterController::class);

        // Role Management
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::get('roles/{role}/edit-permissions', [
            RoleController::class, 'editPermissions'
        ])->name('roles.edit-permissions');
        Route::put('roles/{role}/update-permissions', [
            RoleController::class, 'updatePermissions'
        ])->name('roles.update-permissions');

        // Permission Management
        Route::resource('permissions', PermissionController::class)
            ->only(['index']);

        // Notification Sending
        Route::post('send-notification', [
            NotificationController::class, 'sendGeneralNotification',
        ])->name('send-notification');

        // Backup & Recovery
        Route::get('backups', [
            BackupController::class, 'index',
        ])->name('backups.index');
        Route::post('backups/create', [
            BackupController::class, 'create',
        ])->name('backups.create');
        Route::get('backups/download/{filename}', [
            BackupController::class, 'download',
        ])->name('backups.download');
        Route::post('backups/restore/{filename}', [
            BackupController::class, 'restore',
        ])->name('backups.restore');
        Route::delete('backups/{filename}', [
            BackupController::class, 'destroy',
        ])->name('backups.destroy');
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

require __DIR__ . '/auth.php';
