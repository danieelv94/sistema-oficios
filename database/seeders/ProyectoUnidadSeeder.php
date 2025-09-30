<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Proyecto;
use App\Models\UnidadAdministrativa;

class ProyectoUnidadSeeder extends Seeder
{
    public function run()
    {
        // Crear Unidades Administrativas (usamos firstOrCreate para evitar duplicados)
        $ua1101 = UnidadAdministrativa::firstOrCreate(['clave' => '1101'], ['nombre' => 'DIRECCIÓN DE ADMINISTRACIÓN Y FINANZAS']);
        $ua1102 = UnidadAdministrativa::firstOrCreate(['clave' => '1102'], ['nombre' => 'DIRECCIÓN DE VINCULACIÓN Y PROYECTOS HÍDRICOS']);
        $ua1103 = UnidadAdministrativa::firstOrCreate(['clave' => '1103'], ['nombre' => 'DIRECCIÓN DE CALIDAD DEL AGUA']);
        $ua1104 = UnidadAdministrativa::firstOrCreate(['clave' => '1104'], ['nombre' => 'DIRECCIÓN JURIDICA Y UNIDAD PARA LA IGUALDAD ENTRE HOMBRES Y MUJERES']);
        $ua1105 = UnidadAdministrativa::firstOrCreate(['clave' => '1105'], ['nombre' => 'DIRECCIÓN DE ORGANISMOS OPERADORES Y ATENCIÓN A USUARIOS']);
        $ua1106 = UnidadAdministrativa::firstOrCreate(['clave' => '1106'], ['nombre' => 'DIRECCIÓN DE PLANEACIÓN ESTRATÉGICA']);
        $ua1107 = UnidadAdministrativa::firstOrCreate(['clave' => '1107'], ['nombre' => 'DIRECCIÓN DE GESTIÓN INSTITUCIONAL']);
        $ua1108 = UnidadAdministrativa::firstOrCreate(['clave' => '1108'], ['nombre' => 'DIRECCIÓN DE INFRAESTRUCTURA HIDRÁULICA']);
        $ua1109 = UnidadAdministrativa::firstOrCreate(['clave' => '1109'], ['nombre' => 'DIRECCIÓN GENERAL']);

        // Crear Proyectos y sus relaciones
        $p1 = Proyecto::firstOrCreate(['nombre' => 'ESTRATEGIAS PARA EL CUIDADO DEL AGUA Y MEDIO AMBIENTE REALIZADAS']);
        $p1->unidadesAdministrativas()->sync([$ua1105->id]);

        $p2 = Proyecto::firstOrCreate(['nombre' => 'ESTRATEGIAS PARA FORTALECER LA OPERACIÓN HIDRICA']);
        $p2->unidadesAdministrativas()->sync([$ua1101->id, $ua1104->id, $ua1105->id, $ua1107->id, $ua1109->id]);

        $p3 = Proyecto::firstOrCreate(['nombre' => 'ESTRATEGIAS PARA MEJORAR LA CALIDAD DEL AGUA IMPLEMENTADAS']);
        $p3->unidadesAdministrativas()->sync([$ua1103->id]);

        $p4 = Proyecto::firstOrCreate(['nombre' => 'OBRA PÚBLICA PARA SERVICIOS DE AGUA POTABLE, ALCANTARILLADO Y SANEAMIENTO CONCLUIDA']);
        $p4->unidadesAdministrativas()->sync([$ua1102->id, $ua1105->id, $ua1106->id, $ua1108->id]);
    }
}