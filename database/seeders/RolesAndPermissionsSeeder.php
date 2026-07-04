<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions (Agent Skill Rule #3)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define Permissions based on WMS Blueprint & Vision (Resource.Action notation)
        $permissions = [
            'dashboard.view',
            'user.manage',
            'location.manage',
            'category.manage',
            'product.manage',
            'inventory.view',
            'inventory.update',     // For cycle counts and stock adjustments
            'variance.approve',     // Manager feature from Vision document
            'shipment.receive',
            'order.pick',
            'report.view',
            'transaction.view',
        ];

        // Create Permissions safely
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Define Roles and Assign Permissions

        // Receiving: Need intuitive, fast scanning interfaces for daily receiving
        $receivingRole = Role::firstOrCreate(['name' => 'Receiving']);
        $receivingRole->syncPermissions([
            'dashboard.view', 
            'inventory.view', 
            'shipment.receive'
        ]);

        // Picker: Require optimized pick lists and easy verification tools
        $pickerRole = Role::firstOrCreate(['name' => 'Picker']);
        $pickerRole->syncPermissions([
            'dashboard.view', 
            'inventory.view', 
            'order.pick'
        ]);

        // Clerk: Inventory Clerks for daily receiving, stock updates, cycle counting
        $clerkRole = Role::firstOrCreate(['name' => 'Clerk']);
        $clerkRole->syncPermissions([
            'dashboard.view',
            'inventory.view',
            'inventory.update',
            'shipment.receive',
            'transaction.view',
        ]);

        // Manager: Reporting, analytics, access control, variances approval
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->syncPermissions([
            'dashboard.view',
            'location.manage',
            'category.manage',
            'product.manage',
            'inventory.view',
            'inventory.update',
            'variance.approve',
            'report.view',
            'transaction.view',
        ]);

        // Admin: General administration of the WMS application
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());

        // Super Admin: Bypasses all checks via Gate::before (Agent Skill Rule #2)
        Role::firstOrCreate(['name' => 'Super Admin']);
    }
}
