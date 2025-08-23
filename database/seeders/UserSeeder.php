<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Usuario Administrador (puede ver todo)
        User::create([
            'name' => 'Admin General',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'area_id' => 1
        ]);

        // Usuario Jefe de Área (puede asignar a su gente)
        User::create([
            'name' => 'Jefe de Jurídico',
            'email' => 'jefe.juridico@sistema.com',
            'password' => Hash::make('password'),
            'role' => 'jefe_area',
            'area_id' => 3
        ]);

        // Usuario Normal
        User::create([
            'name' => 'Analista Jurídico',
            'email' => 'analista.juridico@sistema.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'area_id' => 3
        ]);
    }
}