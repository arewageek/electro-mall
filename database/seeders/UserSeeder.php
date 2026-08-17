<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['first_name' => 'Super', 'last_name' => 'Admin', 'email' => 'superadmin@example.com', 'role' => 'super_admin'],
            ['first_name' => 'System', 'last_name' => 'Admin', 'email' => 'admin@example.com', 'role' => 'admin'],
            ['first_name' => 'Warehouse', 'last_name' => 'Manager', 'email' => 'manager@example.com', 'role' => 'manager'],
            ['first_name' => 'Inventory', 'last_name' => 'Clerk', 'email' => 'clerk@example.com', 'role' => 'clerk'],
            ['first_name' => 'Order', 'last_name' => 'Picker', 'email' => 'picker@example.com', 'role' => 'picker'],
            ['first_name' => 'Dock', 'last_name' => 'Receiver', 'email' => 'receiving@example.com', 'role' => 'receiving'],
            ['first_name' => 'Test', 'last_name' => 'User', 'email' => 'test@example.com', 'role' => 'super_admin'],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'first_name' => $u['first_name'],
                    'last_name' => $u['last_name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$u['role']]);
        }
    }
}
