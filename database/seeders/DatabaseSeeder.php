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

        User::factory()->create([
            'profile_pic'       => 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse3.mm.bing.net%2Fth%2Fid%2FOIP.DqCEDqO18zRENq1kW0FVxAHaHa%3Fcb%3D12%26pid%3DApi&f=1&ipt=7b543ffbf7325518cfb398a61c9e7245dcb2a37bbb5087893a075036ee26e434&ipo=images',
            'username'          => 'admin',
            'name'              => 'Administrator',
            'email'             => 'admin@example.com',
            'bio'               => 'System administrator with full access rights.',
            'email_verified_at' => now(),
            'password'          => Hash::make('password')
        ]);
    }
}
