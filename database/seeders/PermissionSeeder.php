<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Create Products', 'slug' => 'products.create', 'description' => 'Create new products'],
            ['name' => 'Update Products', 'slug' => 'products.update', 'description' => 'Update existing products'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'description' => 'Delete products'],
            ['name' => 'View Products', 'slug' => 'products.view', 'description' => 'View products'],
            ['name' => 'Manage Orders', 'slug' => 'orders.manage', 'description' => 'Manage all orders'],
            ['name' => 'View Orders', 'slug' => 'orders.view', 'description' => 'View orders'],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'description' => 'Manage users'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'description' => 'Manage roles and permissions'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['slug' => $permission['slug']], $permission);
        }
    }
}

