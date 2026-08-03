<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Area::create(['name' => 'Dirección General']); // ID 1
        Area::create(['name' => 'Dirección de Gestión Institucional']); // ID 2
        Area::create(['name' => 'Jurídico']); // ID 3
        Area::create(['name' => 'Dirección de Administración y Finanzas']); // ID 4
        Area::create(['name' => 'Dirección de Vinculación y Proyectos Hídricos']); // ID 5
        Area::create(['name' => 'Dirección de Calidad del Agua']); // ID 6
        Area::create(['name' => 'Dirección de Organismos Operadores y Atención a Usuarios']); // ID 7
        Area::create(['name' => 'Dirección de Planeación Estratégica']); // ID 8
        Area::create(['name' => 'Dirección de Infraestructura Hidráulica']); // ID 9
        Area::create(['name' => 'Recursos Humanos']); // ID 10
        Area::create(['name' => 'Sistemas']); // ID 11
    }
}
