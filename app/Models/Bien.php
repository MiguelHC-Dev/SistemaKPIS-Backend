<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bien extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bienes';

    protected $fillable = [
        'nombre',
        'categoria_id',
        'cantidad',
        'ubicacion_id',
        'estado_id',
        'usuario_registro_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaBien::class, 'categoria_id');
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
    }

    // Corrige la relación estado para especificar la clave foránea
    public function estado()
    {
        return $this->belongsTo(EstadoBien::class, 'estado_id');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'usuario_registro_id');
    }
}
