<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\delete;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Administrator');
});

test('an admin can view a list of roles', function () {
    actingAs($this->admin)
        ->get(route('admin.roles.index'))
        ->assertStatus(200);
});

test('an admin can create a new role', function () {
    $roleData = ['name' => 'Test Role', 'description' => 'A test role'];

    actingAs($this->admin)
        ->post(route('admin.roles.store'), $roleData)
        ->assertRedirect(route('admin.roles.index'));

    $this->assertDatabaseHas('roles', ['name' => 'Test Role']);
});

test('an admin can view a single role', function () {
    $role = Role::create(['name' => 'Test Role']);

    actingAs($this->admin)
        ->get(route('admin.roles.show', $role))
        ->assertStatus(200);
});

test('an admin can update a role', function () {
    $role = Role::create(['name' => 'Test Role']);
    $updatedData = ['name' => 'Updated Role Name', 'description' => 'Updated description'];

    actingAs($this->admin)
        ->put(route('admin.roles.update', $role), $updatedData)
        ->assertRedirect(route('admin.roles.index'));

    $this->assertDatabaseHas('roles', ['name' => 'Updated Role Name']);
});

test('an admin can delete a role', function () {
    $role = Role::create(['name' => 'Test Role']);

    actingAs($this->admin)
        ->delete(route('admin.roles.destroy', $role))
        ->assertRedirect(route('admin.roles.index'));

    $this->assertDatabaseMissing('roles', ['name' => 'Test Role']);
});
