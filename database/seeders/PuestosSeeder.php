<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Puesto;

class PuestosSeeder extends Seeder
{
    public function run()
    {
        Puesto::create([
            'nombre' => 'Gerente',
            'descripcion' => 'Encargado de la administración'
        ]);

        Puesto::create([
            'nombre' => 'Supervisor',
            'descripcion' => 'Supervisa operaciones'
        ]);

        Puesto::create([
            'nombre' => 'Vendedor',
            'descripcion' => 'Atención a clientes y ventas'
        ]);
    }
}
