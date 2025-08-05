<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ubicacion;

class UbicacionesSeeder extends Seeder
{
    public function run()
    {
        Ubicacion::create([
            'nombre' => 'Almacén A',
            'descripcion' => 'Almacén principal'
        ]);

        Ubicacion::create([
            'nombre' => 'Oficina Administrativa',
            'descripcion' => 'Área de oficinas'
        ]);

        Ubicacion::create([
            'nombre' => 'Sala de Ventas',
            'descripcion' => 'Área de exhibición'
        ]);
    }
}
