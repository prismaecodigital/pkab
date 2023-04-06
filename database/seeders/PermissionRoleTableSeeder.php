<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $admin_permissions = Permission::all();
        Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));
        $user_permissions = Permission::where('title', 'pkab_access')
                            ->orWhere('title','pkab_item_access')
                            ->orWhere('title','pkab_item_show')
                            ->orWhere('title','user_acc')
                            ->orWhere('title','pkab_item_create');
        $purchasing_permissions = Permission::whereNot('title', 'leader_acc')
                            ->whereNot('title','user_acc');
        $leader_permissions = Permission::where('title', 'pkab_access')
                            ->orWhere('title','pkab_item_access')
                            ->orWhere('title','pkab_item_show')
                            ->orWhere('title','pkab_item_edit')
                            ->orWhere('title','leader_acc')
                            ->orWhere('title','pkab_item_create');
        Role::findOrFail(2)->permissions()->sync($user_permissions->pluck('id'));
        Role::findOrFail(3)->permissions()->sync($purchasing_permissions->pluck('id'));
        Role::findOrFail(4)->permissions()->sync($leader_permissions->pluck('id'));
    }
}