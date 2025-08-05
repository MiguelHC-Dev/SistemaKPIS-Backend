<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsistenciasTable extends Migration
{
    public function up()
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados');
            $table->date('fecha');
            $table->enum('estado', ['Presente', 'Ausente con justificación', 'Ausente injustificado']);
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_registro_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['empleado_id', 'fecha']);
            $table->index('fecha');
        });
    }

    public function down()
    {
        Schema::dropIfExists('asistencias');
    }
}
