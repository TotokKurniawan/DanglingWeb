<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed base roles for web and api guards.
     */
    public function run(): void
    {
        // Web guard roles
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // API guard roles
        Role::firstOrCreate(['name' => 'buyer', 'guard_name' => 'api']);
        Role::firstOrCreate(['name' => 'seller', 'guard_name' => 'api']);
    }
}

