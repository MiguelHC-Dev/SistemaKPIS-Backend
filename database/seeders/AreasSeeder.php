<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;

class AreasSeeder extends Seeder
{
    public function run()
    {
        Area::create([
            'nombre' => 'Consumo en Restaurante',
            'descripcion' => 'Pedidos que se consumen en el lugar.'
        ]);

        Area::create([
            'nombre' => 'Orden para Llevar',
            'descripcion' => 'Clientes que recogen el pedido y se lo llevan.'
        ]);

        Area::create([
            'nombre' => 'Entrega a Domicilio',
            'descripcion' => 'Pedidos por apps o servicio propio (Rappi, Uber Eats, Didi Food, etc.)'
        ]);
    }
}
