<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database with an admin user.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@hdc.com',
            'password' => Hash::make('123456789'), // Change this to a secure password
            'email_verified_at' => now(),
        ]);

        // Assign admin role if it exists
        if (class_exists('Spatie\Permission\Models\Role')) {
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => 'admin'],
                ['guard_name' => 'web']
            );
            $admin->assignRole($adminRole);
        }
    }
}
