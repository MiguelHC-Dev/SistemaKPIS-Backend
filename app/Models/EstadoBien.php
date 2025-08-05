<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadoBien extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nombre'];

    protected $table = 'estado_bienes';
    public function bienes()
    {
        return $this->hasMany(Bien::class);
    }
}
