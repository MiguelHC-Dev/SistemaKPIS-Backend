<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoBien;

class EstadosBienSeeder extends Seeder
{
    public function run()
    {
        EstadoBien::create(['nombre' => 'Nuevo']);
        EstadoBien::create(['nombre' => 'Usado']);
        EstadoBien::create(['nombre' => 'Deteriorado']);
        EstadoBien::create(['nombre' => 'Irreparable']);
    }
}
