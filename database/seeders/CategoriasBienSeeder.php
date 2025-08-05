<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaBien;

class CategoriasBienSeeder extends Seeder
{
    public function run()
    {
        CategoriaBien::create([
            'nombre' => 'Equipo de cómputo',
            'descripcion' => 'Computadoras, laptops y accesorios'
        ]);

        CategoriaBien::create([
            'nombre' => 'Mobiliario',
            'descripcion' => 'Mesas, sillas y escritorios'
        ]);

        CategoriaBien::create([
            'nombre' => 'Equipo electrónico',
            'descripcion' => 'Impresoras, scanners, etc.'
        ]);
    }
}
