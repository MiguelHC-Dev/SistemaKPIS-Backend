<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('rol', ['Administrador', 'Supervisor'])->default('Supervisor');
            $table->boolean('activo')->default(true);
            $table->renameColumn('name', 'nombre_completo');
            $table->renameColumn('password', 'password_hash');
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rol', 'activo']);
            $table->renameColumn('nombre_completo', 'name');
            $table->renameColumn('password_hash', 'password');
            $table->dropSoftDeletes();
        });
    }
}
