<?php

namespace Database\Seeders;

use App\Models\Rol;
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
            Rol::create(['nombre' => $role]);
        }

        User::create([
            'username' => 'admin',
            'dni' => '12345678',
            'password' => 'Admin123$',
            'nombres' => 'Administrador',
            'apellidos' => 'Redes',
            'whatsapp_number' => '987654321',
            'role_id' => 1,
        ]);
    }
}
