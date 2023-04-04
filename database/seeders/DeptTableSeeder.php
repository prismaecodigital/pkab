<?php

namespace Database\Seeders;

use App\Models\Dept;
use Illuminate\Database\Seeder;

class DeptTableSeeder extends Seeder
{
    public function run()
    {
        $depts = [
            [
                'name'  => 'Financial Accounting',
                'code'  => 'FA',
                'bu_id' => 1
            ],
            [
                'name'  => 'Integrated Management System',
                'code'  => 'IMS',
                'bu_id' => 1
            ],
            [
                'name'  => 'Audit & DIgitalisasi',
                'code'  => 'AUD',
                'bu_id' => 1
            ],
            [
                'name'  => 'Kitchen',
                'code'  => 'KC',
                'bu_id' => 3
            ],
            [
                'name'  => 'Senior Care',
                'code'  => 'SC',
                'bu_id' => 2
            ],
            [
                'name'  => 'Home Care',
                'code'  => 'HC',
                'bu_id' => 2
            ],
        ];

        Dept::insert($depts);
    }
}