<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // receiving
        $receiving_role = Role::firstOrCreate(['name' => 'receiving']);
        $receiving_role->syncPermissions([
            'dashboard.view',
            'inventory.view',
            'shipment.receive',
        ]);

        // picker
        $picker_role = Role::firstOrCreate(['name' => 'picker']);
        $picker_role->syncPermissions([
            'dashboard.view',
            'inventory.view',
            'order.pick',
        ]);

        // clerk
        $clerk_role = Role::firstOrCreate(['name' => 'clerk']);
        $clerk_role->syncPermissions([
            'dashboard.view',
            'inventory.view',
            'inventory.update',
            'inventory.count',
            'shipment.receive',
            'transaction.view',
        ]);

        // manager
        $manager_role = Role::firstOrCreate(['name' => 'manager']);
        $manager_role->syncPermissions([
            'dashboard.view',
            'location.manage',
            'category.manage',
            'product.manage',
            'inventory.view',
            'inventory.update',
            'inventory.count',
            'variance.approve',
            'report.view',
            'transaction.view',
        ]);

        // admin
        $admin_role = Role::firstOrCreate(['name' => 'admin']);
        $admin_role->syncPermissions(Permission::all());

        // super_admin (Bypasses all checks via Gate::before)
        Role::firstOrCreate(['name' => 'super_admin']);
    }
}
