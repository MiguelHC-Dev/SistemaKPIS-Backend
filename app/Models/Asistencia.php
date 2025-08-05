<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asistencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asistencias';

    protected $fillable = [
        'empleado_id',
        'fecha',
        'estado',
        'observaciones',
        'usuario_registro_id'
    ];

    protected $casts = [
        'fecha' => 'date'
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
