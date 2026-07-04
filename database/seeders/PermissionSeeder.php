<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Permissions (Resource.Action notation)
        $permissions = [
            'dashboard.view',
            'user.manage',
            'location.manage',
            'category.manage',
            'product.manage',
            'inventory.view',
            'inventory.update',
            'variance.approve',
            'shipment.receive',
            'order.pick',
            'report.view',
            'transaction.view',
        ];

        // Create Permissions
        foreach ($permissions as $permission_name) {
            Permission::firstOrCreate(['name' => $permission_name]);
        }
    }
}
