<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Vehiculo;
class VehiculoSeeder extends Seeder
{
    public function run() { Vehiculo::firstOrCreate(['placa' => 'HP0934G'], ['tipo' => 'Pick Up', 'marca' => 'Nissan', 'modelo' => '2008']); }
}