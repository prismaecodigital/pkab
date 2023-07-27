<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name'  => 'Admin',
                'username'  => 'admin',
                'password'  => bcrypt('admin'),
                'email' => 'admin@admin.com'
            ]
        ];

        User::insert($users);
    }
}