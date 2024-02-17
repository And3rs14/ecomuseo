<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Permission::create(['name' => 'view-pages']);

        Permission::create(['name' => 'create-users']);
        Permission::create(['name' => 'edit-users']);
        Permission::create(['name' => 'delete-users']);

        Permission::create(['name' => 'create-blog-posts']);
        Permission::create(['name' => 'edit-blog-posts']);
        Permission::create(['name' => 'delete-blog-posts']);

        $userRole = Role::create(['name' => 'Visitor']);
        $adminRole = Role::create(['name' => 'Admin']);
        $volunteerRole = Role::create(['name' => 'Volunteer']);

        $userRole->givePermissionTo([
            'view-pages',
        ]);

        $adminRole->givePermissionTo([
            'create-users',
            'edit-users',
            'delete-users',
            'create-blog-posts',
            'edit-blog-posts',
            'delete-blog-posts',
        ]);

        $volunteerRole->givePermissionTo([
            'create-blog-posts',
            'edit-blog-posts',
            'delete-blog-posts',
        ]);

        // $user = User::first();

        // $user->assignRole('Admin');


    }
}