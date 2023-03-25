<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('name', '!=', 'superman')->get();

        $roles = Role::all()->pluck('name')->toArray();

        foreach ($users as $user) {
            $user->assignRole($roles[rand(0, Role::count() - 1)]);
        }
    }
}
