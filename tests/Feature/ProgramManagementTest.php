<?php

use App\Models\Program;
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

test('an admin can view a list of programs', function () {
    actingAs($this->admin)
        ->get(route('admin.programs.index'))
        ->assertStatus(200);
});

test('an admin can create a new program', function () {
    $programData = Program::factory()->make()->toArray();

    actingAs($this->admin)
        ->post(route('admin.programs.store'), $programData)
        ->assertRedirect(route('admin.programs.index'));

    $this->assertDatabaseHas('programs', ['name' => $programData['name']]);
});

test('an admin can view a single program', function () {
    $program = Program::factory()->create();

    actingAs($this->admin)
        ->get(route('admin.programs.show', $program))
        ->assertStatus(200);
});

test('an admin can update a program', function () {
    $program     = Program::factory()->create();
    $updatedData = Program::factory()->make()->toArray();

    actingAs($this->admin)
        ->put(route('admin.programs.update', $program), $updatedData)
        ->assertRedirect(route('admin.programs.index'));

    $this->assertDatabaseHas('programs', ['name' => $updatedData['name']]);
});

test('an admin can delete a program', function () {
    $program = Program::factory()->create();

    actingAs($this->admin)
        ->delete(route('admin.programs.destroy', $program))
        ->assertRedirect(route('admin.programs.index'));

    $this->assertSoftDeleted('programs', ['id' => $program->id]);
});
