<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('usuario')->unique();
            $table->string('email')->unique();
            $table->string('password');

            // 1 = Administrador
            // 2 = Usuario
            // 3 = Profesor
            $table->unsignedTinyInteger('tipo_usuario')->default(2);

            $table->string('imagen_perfil')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfiles');
    }
};
