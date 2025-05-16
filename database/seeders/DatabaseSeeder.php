<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // call the RoleSeeder
        $this->call(RoleSeeder::class);

        // User::factory(10)->create();

        if (!User::where('email', 'superadmin@kaskopi.com')->exists()) {
            User::factory()->create([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@kaskopi.com',
                'role_id' => 1
            ]);
        }

    }
}
