<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBienesTable extends Migration
{
    public function up()
    {
        Schema::create('bienes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->foreignId('categoria_id')->constrained('categoria_bienes');
            $table->integer('cantidad')->default(1);
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            $table->foreignId('estado_id')->constrained('estado_bienes');
            $table->foreignId('usuario_registro_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bienes');
    }
}
