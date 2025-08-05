<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable, SoftDeletes;


    protected $fillable = [
        'nombre_completo',
        'email',
        'password_hash',
        'rol',
        'activo'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Relaciones
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'usuario_registro_id');
    }

    public function bienes()
    {
        return $this->hasMany(Bien::class, 'usuario_registro_id');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'usuario_registro_id');
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'usuario_registro_id');
    }

    public function isAdmin()
    {
        return $this->rol === 'Administrador';
    }
}
