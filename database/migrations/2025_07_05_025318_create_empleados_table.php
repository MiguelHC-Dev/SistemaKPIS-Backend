<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpleadosTable extends Migration
{
    public function up()
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo', 100);
            $table->foreignId('puesto_id')->constrained('puestos');
            $table->foreignId('area_id')->constrained('areas');
            $table->date('fecha_registro')->default(now());
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre_completo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('empleados');
    }
}
