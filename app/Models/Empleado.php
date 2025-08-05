<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'empleados';

    protected $fillable = [
        'nombre_completo',
        'puesto_id',
        'area_id',
        'fecha_registro',
        'activo'
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'activo' => 'boolean'
    ];

    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class);
    }
}
