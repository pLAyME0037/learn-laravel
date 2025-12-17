<?php

use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TransactionLedgerController;
use App\Http\Controllers\UserController;
use App\Livewire\Academic\Dashboard as AcademicDashboard;
use App\Livewire\Academic\ScheduleViewer;
use App\Livewire\Academic\StudentFinancials;
use App\Livewire\Academic\TranscriptViewer;
use App\Livewire\Admin\Academic\BatchEnrollment;
use App\Livewire\Admin\Academic\CalendarManager;
use App\Livewire\Admin\Academic\CourseManager;
use App\Livewire\Admin\Academic\CurriculumBuilder;
use App\Livewire\Admin\Academic\ScheduleManager;
use App\Livewire\Admin\Academic\StructureManager;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Finance\InvoiceList;
use App\Livewire\Admin\Instructors\InstructorForm;
use App\Livewire\Admin\Instructors\InstructorList;
use App\Livewire\Admin\Settings\DictionaryManager;
use App\Livewire\Admin\Settings\SystemSettings;
use App\Livewire\Admin\Students\StudentForm;
use App\Livewire\Admin\Students\StudentList;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Instructor\Dashboard as InstructorDashboard;
use App\Livewire\Instructor\Gradebook;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('dashboard', function () {
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

/*
|--------------------------------------------------------------------------
| Admin / Staff Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', AdminDashboard::class)
            ->name('dashboard');

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

        Route::post('users/{user}/status', [
            UserController::class, 'updateStatus',
        ])->name('users.status');
        Route::post('users/{user}/restore', [
            UserController::class, 'restore',
        ])->name('users.restore')->withTrashed();
        Route::delete('users/{user}/force-delete', [
            UserController::class, 'forceDelete',
        ])->name('users.force-delete');

        Route::get('user-management', UserManagement::class)
            ->name('users.management');

        Route::get('login-history', [
            LoginHistoryController::class, 'index',
        ])->name('login-histories.index');

        Route::get('/academic/batch-enroll', BatchEnrollment::class)
            ->name('academic.batch-enroll');

        Route::resource('attendances', AttendanceController::class);

        Route::resource('audit-logs', AuditLogController::class);

        Route::resource('classrooms', ClassroomController::class);

        Route::resource('roles', RoleController::class);

        Route::get('roles/{role}/edit-permissions', [
            RoleController::class, 'editPermissions',
        ])->name('roles.edit-permissions');
        Route::put('roles/{role}/update-permissions', [
            RoleController::class, 'updatePermissions',
        ])->name('roles.update-permissions');

        Route::resource('semesters', SemesterController::class);

        Route::resource('transaction-ledgers', TransactionLedgerController::class);

        Route::resource('permissions', PermissionController::class)->except(['show']);

        // Notification Sending
        // Route::post('send-notification', [
        //     NotificationController::class, 'sendGeneralNotification',
        // ])->name('send-notification');

        // // Backup & Recovery
        // Route::get('backups', [
        //     BackupController::class, 'index',
        // ])->name('backups.index');
        // Route::post('backups/create', [
        //     BackupController::class, 'create',
        // ])->name('backups.create');
        // Route::get('backups/download/{filename}', [
        //     BackupController::class, 'download',
        // ])->name('backups.download');
        // Route::post('backups/restore/{filename}', [
        //     BackupController::class, 'restore',
        // ])->name('backups.restore');
        // Route::delete('backups/{filename}', [
        //     BackupController::class, 'destroy',
        // ])->name('backups.destroy');

        Route::get('/settings/dictionaries', DictionaryManager::class)
            ->name('settings.dictionaries');

        Route::get('/settings/system', SystemSettings::class)
            ->name('settings.system');

        Route::get('/manager/structure', StructureManager::class)
            ->name('manager.structure');

        Route::get('/manager/calender', CalendarManager::class)
            ->name('manager.calender');

        // Course Catalog
        Route::get('/courses', CourseManager::class)
            ->name('courses.index');

        // Curriculum Builder (Must pass Program ID)
        Route::get('/programs/{program}/curriculum', CurriculumBuilder::class)
            ->name('programs.curriculum');

        // Academic Schedule
        Route::get('/academic/schedule', ScheduleManager::class)
            ->name('academic.schedule');

        // Finance
        Route::get('/finance/invoices', InvoiceList::class)
            ->name('finance.invoices');

        // === STUDENTS ===
        // List (Livewire)
        Route::get('/students', StudentList::class)
            ->name('students.index');
        // Create (Livewire Form)
        Route::get('/students/create', StudentForm::class)
            ->name('students.create');
        // Edit (Livewire Form - pass ID)
        Route::get('/students/{studentId}/edit', StudentForm::class)
            ->name('students.edit');
        // Show (Controller -> Blade View)
        Route::get('/students/{student}', [
            StudentController::class, 'show',
        ])->name('students.show');

        // === INSTRUCTORS ===
        // List (Livewire)
        Route::get('/instructors', InstructorList::class)
            ->name('instructors.index');
        // Create
        Route::get('/instructors/create', InstructorForm::class)
            ->name('instructors.create');
        // Edit
        Route::get('/instructors/{instructorId}/edit', InstructorForm::class)
            ->name('instructors.edit');
        // Show
        Route::get('/instructors/{instructor}', [
            InstructorController::class, 'show',
        ])->name('instructors.show');
    });

/*
|--------------------------------------------------------------------------
| Student / Academic Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
->prefix('academic')
->name('academic.')
->group(function () {

Route::get('/dashboard', AcademicDashboard::class)->name('dashboard');
// Weekly Schedule
Route::get('/schedule', ScheduleViewer::class)->name('schedule');

Route::get('/finance', StudentFinancials::class)->name('finance');

Route::get('/transcript', TranscriptViewer::class)->name('transcript');

});

/*
|--------------------------------------------------------------------------
| Instructor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
->prefix('instructor')
->name('instructor.')
->group(function () {

Route::get('/dashboard', InstructorDashboard::class)->name('dashboard');
Route::get('/gradebook/{classSessionId}', Gradebook::class)->name('gradebook');

});
/*
|--------------------------------------------------------------------------
| Theme Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
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
