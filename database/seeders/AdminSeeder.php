<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'profile_pic'       => 'https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse3.mm.bing.net%2Fth%2Fid%2FOIP.DqCEDqO18zRENq1kW0FVxAHaHa%3Fcb%3D12%26pid%3DApi&f=1&ipt=7b543ffbf7325518cfb398a61c9e7245dcb2a37bbb5087893a075036ee26e434&ipo=images',
                'name'              => 'Administrator',
                'username'          => 'admin',
                'email'             => 'admin@example.com',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
                'bio'               => 'System Administrator',
            ]);
        }
    }
}
