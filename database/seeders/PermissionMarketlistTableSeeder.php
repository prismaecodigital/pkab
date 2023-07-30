<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class PermissionMarketlistTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        // $permissions = [
        //     [
        //         'title' => 'marketlist_access',
        //     ],
        //     [
        //         'title' => 'marketlist_edit',
        //     ],
        //     [
        //         'title' => 'marketlist_delete',
        //     ],
        //     [
        //         'title' => 'marketlist_show',
        //     ],
        //     [
        //         'title' => 'marketlist_create',
        //     ],
        //     [
        //         'title' => 'marketlist_approve',
        //     ],
        //     [
        //         'title' => 'marketlistItem_access',
        //     ],
        //     [
        //         'title' => 'marketlistItem_edit',
        //     ],
        //     [
        //         'title' => 'marketlistItem_delete',
        //     ],
        //     [
        //         'title' => 'marketlistItem_show',
        //     ],
        //     [
        //         'title' => 'marketlistItem_create',
        //     ],
        //     [
        //         'title' => 'purchasing_ml_1',
        //     ],
        //     [
        //         'title' => 'purchasing_ml_2',
        //     ],
        // ];

        // $permission = Permission::insert($permissions);

        $marketlist_permissions = Permission::all();
        $purchasing_permissions = Permission::whereNot('title', 'leader_acc')
        ->whereNot('title','user_acc');
        Role::findOrFail(1)->permissions()->syncWithoutDetaching($marketlist_permissions->pluck('id'));
        Role::findOrFail(3)->permissions()->syncWithoutDetaching($purchasing_permissions->pluck('id'));
        
    }
}
