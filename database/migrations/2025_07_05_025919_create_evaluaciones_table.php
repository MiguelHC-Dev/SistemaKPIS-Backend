<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluacionesTable extends Migration
{
    public function up()
    {
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados');
            $table->tinyInteger('mes');
            $table->smallInteger('anio');
            $table->tinyInteger('puntuacion');
            $table->string('comentarios', 200)->nullable();
            $table->foreignId('usuario_registro_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['empleado_id', 'mes', 'anio']);
            $table->index(['mes', 'anio']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluaciones');
    }
}
