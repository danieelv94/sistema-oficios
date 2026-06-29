<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubareaSeeder extends Seeder
{
    public function run()
    {
        $subareas = [
            // Dirección de Gestión Institucional (area_id = 2)
            ['name' => 'Subdirección de Enlace Institucional y Seguimiento', 'area_id' => 2, 'prefijo' => 'SEIS'],
            ['name' => 'Subdirección de Informática y Transparencia', 'area_id' => 2, 'prefijo' => 'SIT'],
            ['name' => 'Subdirección de Archivo Institucional', 'area_id' => 2, 'prefijo' => 'SAI'],

            // Dirección de Infraestructura Hidráulica (area_id = 8)
            ['name' => 'Subdirección de Control y Seguimiento de Obra', 'area_id' => 8, 'prefijo' => 'SCSO'],
            ['name' => 'Subdirección de Construcción de Obra', 'area_id' => 8, 'prefijo' => 'SCO'],

            // Dirección de Vinculación y Proyectos Hídricos (area_id = 7)
            ['name' => 'Subdirección de Recursos Hídricos', 'area_id' => 7, 'prefijo' => 'SRH'],
            ['name' => 'Subdirección de Estudios y Proyectos Hídricos', 'area_id' => 7, 'prefijo' => 'SEPH'],

            // Dirección de Organismos Operadores y Atención a Usuarios (area_id = 5)
            ['name' => 'Subdirección de Atención a Usuarios y Organismos Operadores', 'area_id' => 5, 'prefijo' => 'SAUOO'],
            ['name' => 'Subdirección de Atención Ciudadana', 'area_id' => 5, 'prefijo' => 'SAC'],
            ['name' => 'Subdirección de Cultura del Agua y Participación Social', 'area_id' => 5, 'prefijo' => 'SCAPS'],

            // Dirección de Planeación Estratégica (area_id = 3)
            ['name' => 'Subdirección de Control Presupuestal', 'area_id' => 3, 'prefijo' => 'SCP'],
            ['name' => 'Subdirección de Seguimiento y Evaluación', 'area_id' => 3, 'prefijo' => 'SSE'],
            ['name' => 'Subdirección de Licitaciones y Precios Unitarios', 'area_id' => 3, 'prefijo' => 'SLPU'],

            // Dirección de Administración y Finanzas (area_id = 4)
            ['name' => 'Subdirección de Contabilidad y Finanzas', 'area_id' => 4, 'prefijo' => 'SCF'],
            ['name' => 'Subdirección de Recursos Materiales y Servicios Generales', 'area_id' => 4, 'prefijo' => 'SRMSG'],
            ['name' => 'Subdirección de Recursos Humanos', 'area_id' => 4, 'prefijo' => 'SRH'],
        ];

        foreach ($subareas as $subarea) {
            DB::table('subareas')->updateOrInsert(
                ['name' => $subarea['name'], 'area_id' => $subarea['area_id']],
                ['prefijo' => $subarea['prefijo'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
