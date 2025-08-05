<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turno extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'turnos';

    protected $fillable = ['nombre', 'hora_inicio', 'hora_fin'];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
