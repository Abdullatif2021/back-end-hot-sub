<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        // Assuming you have already seeded roles in the 'roles' table
        $userRole = Role::where('name', 'user')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $superadminRole = Role::where('name', 'superadmin')->first();

        // Assign 'user' role to users with IDs 1 to 10
        User::whereIn('id', range(1, 10))->each(function ($user) use ($userRole) {
            $user->roles()->attach($userRole);
        });

        // Assign 'admin' role to admins with IDs 1 to 5
        Admin::whereIn('id', range(1, 5))->each(function ($admin) use ($adminRole) {
            $admin->roles()->attach($adminRole);
        });

        // Assign 'superadmin' role to admin with ID 6
        $superAdmin = Admin::find(6);
        if ($superAdmin) {
            $superAdmin->roles()->attach($superadminRole);
        }
    }
}
