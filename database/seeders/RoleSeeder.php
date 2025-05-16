<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            ['name' => 'superadmin', 'description' => 'Super Admin'],
            ['name' => 'admin', 'description' => 'Admin'],
            ['name' => 'user', 'description' => 'User'],
            ['name' => 'employee', 'description' => 'Employee'],
            ['name' => 'customer', 'description' => 'Customer']
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
