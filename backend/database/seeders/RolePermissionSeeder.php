<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========= CREAR ROLES =========
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $computo = Role::firstOrCreate(['name' => 'computo', 'guard_name' => 'web']);
        $secretaria = Role::firstOrCreate(['name' => 'secretaria', 'guard_name' => 'web']);
        
        // ========= PERMISOS =========
        $permisos = [
            // USERS
            'users.view','users.create','users.edit','users.delete',

            // ROLES
            'roles.view','roles.create','roles.edit','roles.delete',

            // CONSTANCIAS
            'constancias.view','constancias.create','constancias.edit','constancias.delete',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // ========= ASIGNACIÓN DE PERMISOS =========

        // SUPER ADMIN: todos los permisos
        $superAdmin->syncPermissions(Permission::all());

        // COMPUTO
        $computo->syncPermissions([
            // CONSTANCIAS
            'constancias.view','constancias.create','constancias.edit','constancias.delete',
        ]);

        // SECRETARIA
        $secretaria->syncPermissions([
            // CONSTANCIAS
            'constancias.view','constancias.create','constancias.edit','constancias.delete',
        ]);
    }
}
