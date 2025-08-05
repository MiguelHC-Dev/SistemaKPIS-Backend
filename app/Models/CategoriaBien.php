<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaBien extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categoria_bienes';


    protected $fillable = ['nombre', 'descripcion'];

    public function bienes()
    {
        return $this->hasMany(Bien::class, 'categoria_id');
    }
}
