<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'evaluaciones';

    protected $fillable = [
        'empleado_id',
        'mes',
        'anio',
        'puntuacion',
        'comentarios',
        'usuario_registro_id'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'usuario_registro_id');
    }
}
