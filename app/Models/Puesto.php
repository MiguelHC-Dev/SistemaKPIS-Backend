<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Puesto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'puestos';

    protected $fillable = ['nombre', 'descripcion'];

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
