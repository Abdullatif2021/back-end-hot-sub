<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Get roles by name
          $superadminRole = Role::where('name', 'superadmin')->first();
          $adminRole = Role::where('name', 'admin')->first();
          $userRole = Role::where('name', 'user')->first();
  
      
  
          // Users
          User::find([1, 2,3, 4, 5,6, 7, 8, 9, 10])->each(function ($user) use ($userRole) {
              $user->roles()->attach($userRole);
          });
    }
}
