<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "username"=> "Superadminnn",
            "email" => "admin@gmail.com",
            "password" => Hash::make("PassWord123"),
            "birthday" => "2002-12-03",
        ]);


        $getListUser = Permission::create(['name' => 'get-list-user', 'cipher' => 'GUL', 'description'=> 'получить список пользователей', 'created_by'=> '1']);
        $getListRole = Permission::create(['name' => 'get-list-role', 'cipher' => 'GUR', 'description'=> 'получить список ролей', 'created_by'=> '1']);
        $getListPermission = Permission::create(['name' => 'get-list-permission', 'cipher' => 'GPL', 'description'=> 'получить список разрешений', 'created_by'=> '1']);
        $readUser = Permission::create(['name' => 'read-user', 'cipher' => 'GIAU', 'description'=> 'получить информацию о пользователях', 'created_by'=> '1']);
        $readRole = Permission::create(['name' => 'read-role', 'cipher' => 'GIAR', 'description'=> 'получить информацию о ролях', 'created_by'=> '1']);
        $readRolesSelf = Permission::create(['name'=> 'read-roles-self', 'cipher' => 'RRS' , 'description' => 'получить информацию о своих ролях', 'created_by' => '1']);
        $readPermission = Permission::create(['name' => 'read-permission', 'cipher' => 'GIAP', 'description'=> 'получить информацию о разрешениях', 'created_by'=> '1']);
        $createUser = Permission::create(['name' => 'create-user', 'cipher' => 'CNU', 'description'=> 'создать пользователя', 'created_by'=> '1']);
        $createRole = Permission::create(['name' => 'create-role', 'cipher' => 'CNR', 'description'=> 'создать роль', 'created_by'=> '1']);
        $createPermission = Permission::create(['name' => 'create-permission', 'cipher' => 'CNP', 'description'=> 'создать разрешение', 'created_by'=> '1']);
        $updateUser = Permission::create(['name' => 'update-user', 'cipher' => 'UEU', 'description'=> 'изменить пользователя', 'created_by'=> '1']);
        $updateRole = Permission::create(['name' => 'update-role', 'cipher' => 'UER', 'description'=> 'изменить роль', 'created_by'=> '1']);
        $updatePermission = Permission::create(['name' => 'update-permission', 'cipher' => 'UEP', 'description'=> 'изменить разрешение', 'created_by'=> '1']);
        $deleteUser = Permission::create(['name' => 'delete-user', 'cipher' => 'DU', 'description'=> 'удалить пользователя', 'created_by'=> '1']);
        $deleteRole = Permission::create(['name' => 'delete-role', 'cipher' => 'DR', 'description'=> 'удалить роль', 'created_by'=> '1']);
        $deleteRolesAndUsers = Permission::create(['name' => 'delete-roles-and-users', 'cipher' => 'DRAU', 'description'=> 'удалить связь между ролью и пользователем', 'created_by'=> '1']);
        $deleteRolesAndPermissions = Permission::create(['name' => 'delete-roles-and-permissions', 'cipher' => 'DRAP', 'description'=> 'удалить связь между ролью и разрешением', 'created_by'=> '1']);
        $deletePermission = Permission::create(['name' => 'delete-permission', 'cipher' => 'DP', 'description'=> 'удалить разрешение', 'created_by'=> '1']);
        $softDeletePermission = Permission::create(['name' => 'soft-delete-permission', 'cipher' => 'SDP', 'description'=> 'мягко удалить разрешение', 'created_by'=> '1']);
        $softDeleteRole = Permission::create(['name' => 'soft-delete-role', 'cipher' => 'SDR', 'description'=> 'мягко удалить роль', 'created_by'=> '1']);
        $softDeleteRolesAndUsers = Permission::create(['name' => 'soft-delete-roles-and-users', 'cipher' => 'SDRAU', 'description'=> 'мягко удалить связь между ролью и пользователем', 'created_by'=> '1']);
        $softDeleteRolesAndPermissions = Permission::create(['name' => 'soft-delete-roles-and-permissions', 'cipher' => 'SDRAP', 'description'=> 'мягко удалить связь между ролью и разрешением', 'created_by'=> '1']);
        $restoreUser = Permission::create(['name' => 'restore-user', 'cipher' => 'DPU', 'description'=> 'восстановить пользователя', 'created_by'=> '1']);
        $restoreRole = Permission::create(['name' => 'restore-role', 'cipher' => 'DPR', 'description'=> 'восстановить роль', 'created_by'=> '1']);
        $restoreRolesAndUsers = Permission::create(['name' => 'restore-roles-and-users', 'cipher' => 'RRAU', 'description'=> 'восстанановление связи между пользователем и ролью', 'created_by'=> '1']);
        $restoreRolesAndPermissions = Permission::create(['name' => 'restore-roles-and-permissions', 'cipher' => 'RRAP', 'description'=> 'восстанановление связи между разрешением и ролью', 'created_by'=> '1']);
        $restorePermission = Permission::create(['name' => 'restore-permission', 'cipher' => 'DPP', 'description'=> 'восстановить разрешение', 'created_by'=> '1']);
        $assignRole = Permission::create(['name' => 'assign-role', 'cipher' => 'AR', 'description'=> 'назначить роль', 'created_by'=> '1']);
        $assignPermission = Permission::create(['name' => 'assign-permission', 'cipher' => 'AP', 'description'=> 'назначить разрешение', 'created_by'=> '1']);
        $getListLogs = Permission::create(['name' => 'get-list-logs', 'cipher' => 'GLL', 'description'=> 'Просматривать список логов', 'created_by'=> '1']);
        $getSpecificLog = Permission::create(['name' => 'get-specific-log', 'cipher' => 'GSL', 'description'=> 'Просматривать конкретный лог', 'created_by'=> '1']);
        $deleteSpecificLog = Permission::create(['name' => 'delete-specific-log', 'cipher' => 'DSL', 'description'=> 'Просматривать конкретный лог', 'created_by'=> '1']);
    }
}
