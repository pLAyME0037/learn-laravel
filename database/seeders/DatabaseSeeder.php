<?php
namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create specific types of fake users
        // User::factory(10)->create();
        // User::factory()->count(5)->withoutProfilePic()->create();
        // User::factory()->count(3)->withLongBio()->unverified()->create();

        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            AdminSeeder::class,
        ]);
    }
}
