<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed roles and permissions for web and api guards.
     */
    public function run(): void
    {
        /** @var PermissionRegistrar $permissionRegistrar */
        $permissionRegistrar = app(PermissionRegistrar::class);
        $permissionRegistrar->forgetCachedPermissions();

        // Web guard permissions (admin & operator)
        $webPermissions = [
            'manage_operators',
            'manage_partners',
            'manage_sellers',
            'view_admin_dashboard',
            'view_operator_dashboard',
            'view_sellers',
            'view_complaints',
        ];

        foreach ($webPermissions as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // API guard permissions (buyer & seller)
        $apiPermissions = [
            'manage_products',
            'manage_orders',
            'create_orders',
            'create_complaints',
        ];

        foreach ($apiPermissions as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'api',
            ]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $buyer = Role::firstOrCreate(['name' => 'buyer', 'guard_name' => 'api']);
        $seller = Role::firstOrCreate(['name' => 'seller', 'guard_name' => 'api']);

        // Map roles to permissions (sesuai rekomendasi dokumentasi)
        $admin->syncPermissions([
            'manage_operators',
            'manage_partners',
            'manage_sellers',
            'view_admin_dashboard',
            'view_complaints',
        ]);

        $seller->syncPermissions([
            'manage_products',
            'manage_orders',
        ]);

        $buyer->syncPermissions([
            'create_orders',
            'create_complaints',
        ]);

        $permissionRegistrar->forgetCachedPermissions();
    }
}

