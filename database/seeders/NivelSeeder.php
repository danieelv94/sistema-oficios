<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Nivel;

class NivelSeeder extends Seeder
{
    public function run()
    {
        $niveles = [
            '12B DIRECTOR(A) DE ÁREA B',
            '11B DIRECTOR(A) DE ÁREA B',
            '10C SUBDIRECTOR(A) DE ÁREA C',
            '10A SUBDIRECTOR(A)ADJUNTO(A) A',
            '09C JEFE(A) DE DEPARTAMENTO C',
            '89B JEFE(A) DE ÁREA B',
            '89A JEFE(A) DE ÁREA A',
        ];

        foreach ($niveles as $nivel) {
            Nivel::firstOrCreate(['nombre' => $nivel]);
        }
    }
}