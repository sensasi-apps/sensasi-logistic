<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::whereNot('name', 'superman')->get();

        $roles = [
            'Stackholder',
            'Manufacture',
            'Sales',
            'Warehouse'
        ];

        foreach ($users as $user) {
            $user->assignRole($roles[rand(0, 4)]);
        }
    }
}
