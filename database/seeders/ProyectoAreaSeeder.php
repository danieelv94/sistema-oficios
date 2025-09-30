<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyecto;
use App\Models\Area; // Importante importar el modelo Area

class ProyectoAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Primero, buscamos las áreas existentes por su nombre.
        // Asegúrate de que los nombres coincidan EXACTAMENTE con los que tienes en tu tabla `areas`.
        $areaAdminFinanzas = Area::where('name', 'Dirección de Administración y Finanzas')->first();
        $areaVinculacion = Area::where('name', 'Dirección de Vinculación y Proyectos Hídricos')->first();
        $areaCalidadAgua = Area::where('name', 'Dirección de Calidad del Agua')->first();
        $areaJuridico = Area::where('name', 'Jurídico')->first(); // Asumiendo que se llama así
        $areaOrganismos = Area::where('name', 'Dirección de Organismos Operadores y Atención a Usuarios')->first();
        $areaPlaneacion = Area::where('name', 'Dirección de Planeación Estratégica')->first();
        $areaGestion = Area::where('name', 'Dirección de Gestión Institucional')->first();
        $areaInfraestructura = Area::where('name', 'Dirección de Infraestructura Hidráulica')->first();
        $areaGeneral = Area::where('name', 'Dirección General')->first();

        // --- Proyecto 1 ---
        $p1 = Proyecto::create(['nombre' => 'ESTRATEGIAS PARA EL CUIDADO DEL AGUA Y MEDIO AMBIENTE REALIZADAS']);
        if ($areaOrganismos) {
            $p1->areas()->attach($areaOrganismos->id);
        }

        // --- Proyecto 2 ---
        $p2 = Proyecto::create(['nombre' => 'ESTRATEGIAS PARA FORTALECER LA OPERACIÓN HIDRICA']);
        $areasP2 = [];
        if ($areaAdminFinanzas) $areasP2[] = $areaAdminFinanzas->id;
        if ($areaJuridico) $areasP2[] = $areaJuridico->id;
        if ($areaOrganismos) $areasP2[] = $areaOrganismos->id;
        if ($areaGestion) $areasP2[] = $areaGestion->id;
        if ($areaGeneral) $areasP2[] = $areaGeneral->id;
        $p2->areas()->attach($areasP2);

        // --- Proyecto 3 ---
        $p3 = Proyecto::create(['nombre' => 'ESTRATEGIAS PARA MEJORAR LA CALIDAD DEL AGUA IMPLEMENTADAS']);
        if ($areaCalidadAgua) {
            $p3->areas()->attach($areaCalidadAgua->id);
        }

        // --- Proyecto 4 ---
        $p4 = Proyecto::create(['nombre' => 'OBRA PÚBLICA PARA SERVICIOS DE AGUA POTABLE, ALCANTARILLADO Y SANEAMIENTO CONCLUIDA']);
        $areasP4 = [];
        if ($areaVinculacion) $areasP4[] = $areaVinculacion->id;
        if ($areaOrganismos) $areasP4[] = $areaOrganismos->id;
        if ($areaPlaneacion) $areasP4[] = $areaPlaneacion->id;
        if ($areaInfraestructura) $areasP4[] = $areaInfraestructura->id;
        $p4->areas()->attach($areasP4);
    }
}