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
        Area::create(['name' => 'Dirección General']);
        Area::create(['name' => 'Recursos Humanos']);
        Area::create(['name' => 'Jurídico']);
        Area::create(['name' => 'Sistemas']);
    }
}
