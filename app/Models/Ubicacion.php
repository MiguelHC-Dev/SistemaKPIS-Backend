<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ubicacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ubicaciones';

    protected $fillable = ['nombre', 'descripcion'];

    // Relación corregida con bienes
    public function bienes()
    {
        return $this->hasMany(Bien::class, 'ubicacion_id');
    }
}
