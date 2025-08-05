<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@kpis.com'],
            [
                'nombre_completo' => 'Administrador Principal',
                'password_hash' => Hash::make('Admin123!'),
                'rol' => 'Administrador'
            ]
        );

        $adminRole = Role::where('name', 'Administrador')->first();
        $admin->assignRole($adminRole);

        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@kpis.com'],
            [
                'nombre_completo' => 'Supervisor Ejemplo',
                'password_hash' => Hash::make('Supervisor123!'),
                'rol' => 'Supervisor'
            ]
        );

        $supervisorRole = Role::where('name', 'Supervisor')->first();
        $supervisor->assignRole($supervisorRole);
    }
}
