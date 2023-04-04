<?php

namespace Database\Seeders;

use App\Models\Bu;
use Illuminate\Database\Seeder;

class BuTableSeeder extends Seeder
{
    public function run()
    {
        $bus = [
            [
                'name'  => 'Prisma',
                'code'  => 'PRI',
            ],
            [
                'name'  => 'Rukun Sahabat Senior',
                'code'  => 'RSS',
            ],
            [
                'name'  => 'Pandjava',
                'code'  => 'PKU',
            ],
        ];

        Bu::insert($bus);
    }
}