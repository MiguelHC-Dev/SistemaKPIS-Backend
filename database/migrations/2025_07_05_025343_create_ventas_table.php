<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('turno_id')->constrained('turnos');
            $table->decimal('monto', 10, 2);
            $table->foreignId('area_id')->constrained('areas');
            $table->foreignId('usuario_registro_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['fecha', 'turno_id', 'area_id']);
            $table->index('fecha');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ventas');
    }
}
