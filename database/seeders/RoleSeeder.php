<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run(): void
    {
        $Admin = Role::create(['name' => 'Админ', 'cipher'=> 'admin', 'description'=> 'админ','created_by'=> '1']);

        $User = Role::create(['name' => 'Пользователь', 'cipher'=> 'user', 'description'=> 'пользователь','created_by'=> '1']);

        $Guest = Role::create(['name' => 'Гость', 'cipher'=> 'guest', 'description'=> 'даже не гражданин','created_by'=> '1']);

        $permissions = Permission::all();

        $Admin->permissions()->attach($permissions->pluck('id')->toArray(), ['created_by' => 1]);

        $permissionId = 1;
        $permission = Permission::find($permissionId);

        $User->permissions()->attach($permission, ['created_by' => 1]);

        $Guest->permissions()->attach($permission, ['created_by' => 1]);

        $permissionId = 4;
        $permission = Permission::find($permissionId);

        $User->permissions()->attach($permission, ['created_by' => 1]);

        $permissionId = 10;
        $permission = Permission::find($permissionId);

        $User->permissions()->attach($permission, ['created_by' => 1]);



    }
    }

