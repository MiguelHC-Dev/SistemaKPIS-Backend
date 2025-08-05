<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesSeeder::class,
            TurnosSeeder::class,
            EstadosBienSeeder::class,
            CategoriasBienSeeder::class,
            UbicacionesSeeder::class,
            PuestosSeeder::class,
            AreasSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
