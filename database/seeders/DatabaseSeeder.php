<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Administrador', 'Personal', 'Practicante'];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        $this->call(PermissionSeeder::class);

        $admin = User::create([
            'username' => 'admin',
            'dni' => '12345678',
            'password' => 'Admin123$',
            'nombres' => 'Administrador',
            'apellidos' => 'Redes',
            'whatsapp_number' => '987654321',
        ]);

        $admin->assignRole('Administrador');
    }
}
