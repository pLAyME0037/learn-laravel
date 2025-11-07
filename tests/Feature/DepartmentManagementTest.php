<?php

use App\Models\Department;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Administrator');
});

test('an admin can view a list of departments', function () {
    actingAs($this->admin)
        ->get(route('admin.departments.index'))
        ->assertStatus(200);
});

test('an admin can create a new department', function () {
    $departmentData = Department::factory()->make()->toArray();

    actingAs($this->admin)
        ->post(route('admin.departments.store'), $departmentData)
        ->assertRedirect(route('admin.departments.index'));

    $this->assertDatabaseHas('departments', ['name' => $departmentData['name']]);
});

test('an admin can view a single department', function () {
    $department = Department::factory()->create();

    actingAs($this->admin)
        ->get(route('admin.departments.show', $department))
        ->assertStatus(200);
});

test('an admin can update a department', function () {
    $department  = Department::factory()->create();
    $updatedData = Department::factory()->make()->toArray();

    actingAs($this->admin)
        ->put(route('admin.departments.update', $department), $updatedData)
        ->assertRedirect(route('admin.departments.index'));

    $this->assertDatabaseHas('departments', ['name' => $updatedData['name']]);
});

test('an admin can delete a department', function () {
    $department = Department::factory()->create();

    actingAs($this->admin)
        ->delete(route('admin.departments.destroy', $department))
        ->assertRedirect(route('admin.departments.index'));

    $this->assertSoftDeleted('departments', ['id' => $department->id]);
});
