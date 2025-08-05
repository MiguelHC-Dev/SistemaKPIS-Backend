<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Turno;

class TurnosSeeder extends Seeder
{
    public function run()
    {
        Turno::create([
            'nombre' => 'Mañana',
            'hora_inicio' => '08:00:00',
            'hora_fin' => '14:00:00'
        ]);

        Turno::create([
            'nombre' => 'Tarde',
            'hora_inicio' => '14:00:00',
            'hora_fin' => '20:00:00'
        ]);
    }
}
