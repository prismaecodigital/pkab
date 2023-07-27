<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'title' => 'user_management_access',
            ],
            [
                'title' => 'permission_create',
            ],
            [
                'title' => 'permission_edit',
            ],
            [
                'title' => 'permission_show',
            ],
            [
                'title' => 'permission_delete',
            ],
            [
                'title' => 'permission_access',
            ],
            [
                'title' => 'role_create',
            ],
            [
                'title' => 'role_edit',
            ],
            [
                'title' => 'role_show',
            ],
            [
                'title' => 'role_delete',
            ],
            [
                'title' => 'role_access',
            ],
            [
                'title' => 'user_create',
            ],
            [
                'title' => 'user_edit',
            ],
            [
                'title' => 'user_show',
            ],
            [
                'title' => 'user_delete',
            ],
            [
                'title' => 'user_access',
            ],
            [
                'title' => 'bu_create',
            ],
            [
                'title' => 'bu_edit',
            ],
            [
                'title' => 'bu_show',
            ],
            [
                'title' => 'bu_delete',
            ],
            [
                'title' => 'bu_access',
            ],
            [
                'title' => 'bu_dept_access',
            ],
            [
                'title' => 'dept_create',
            ],
            [
                'title' => 'dept_edit',
            ],
            [
                'title' => 'dept_show',
            ],
            [
                'title' => 'dept_delete',
            ],
            [
                'title' => 'dept_access',
            ],
            [
                'title' => 'pkab_access',
            ],
            [
                'title' => 'pkab_item_create',
            ],
            [
                'title' => 'pkab_item_show',
            ],
            [
                'title' => 'pkab_item_delete',
            ],
            [
                'title' => 'pkab_item_access',
            ],
            [
                'title' => 'pkab_item_edit',
            ],
            [
                'title' => 'leader_acc',
            ],
            [
                'title' => 'purchasing_acc_1',
            ],
            [
                'title' => 'purchasing_acc_2',
            ],
            [
                'title' => 'purchasing_acc_3',
            ],
            [
                'title' => 'user_acc',
            ],
            [
                'title' => 'site_create',
            ],
            [
                'title' => 'site_edit',
            ],
            [
                'title' => 'site_show',
            ],
            [
                'title' => 'site_delete',
            ],
            [
                'title' => 'site_access',
            ],
        ];

        Permission::insert($permissions);
    }
}