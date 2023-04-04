<?php

namespace Database\Seeders;

use App\Models\Dept;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeptUserTableSeeder extends Seeder
{
    public function run()
    {
        $depts = Dept::all();
        User::findOrFail(1)->dept()->sync($depts->pluck('id'));
    }
}