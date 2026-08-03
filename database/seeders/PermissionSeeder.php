<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'ver dashboard',
            'ver perfil propio',
            'ver reportes',
            'gestionar roles',
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'gestionar categorias',
            'gestionar marcas',
            'gestionar objetos',
            'registrar movimientos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::where('name', 'Administrador')->first();
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }

        $personal = Role::where('name', 'Personal')->first();
        if ($personal) {
            $personal->givePermissionTo([
                'ver dashboard',
                'ver perfil propio',
                'ver reportes',
                'ver usuarios',
                'gestionar objetos',
                'registrar movimientos',
            ]);
        }

        $practicante = Role::where('name', 'Practicante')->first();
        if ($practicante) {
            $practicante->givePermissionTo([
                'ver dashboard',
                'ver perfil propio',
            ]);
        }
    }
}
