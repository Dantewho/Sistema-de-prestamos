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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();

            // Usuario que realiza la solicitud
            $table->foreignId('usuario_solicitante_id')
                  ->constrained('perfiles')
                  ->restrictOnDelete();

            // Usuario que registra/autoriza el préstamo
            $table->foreignId('usuario_prestador_id')
                  ->nullable()
                  ->constrained('perfiles')
                  ->nullOnDelete();

            // aula o inventario
            $table->enum('tipo_solicitud', ['aula', 'inventario']);

            // Se utiliza únicamente cuando es préstamo de aula
            $table->foreignId('aula_id')
                  ->nullable()
                  ->constrained('aulas')
                  ->restrictOnDelete();

            // Se utiliza únicamente cuando es préstamo de inventario
            $table->foreignId('inventario_id')
                  ->nullable()
                  ->constrained('inventario')
                  ->restrictOnDelete();

            // Cantidad solicitada cuando es inventario
            $table->unsignedInteger('cantidad')
                  ->nullable();

            $table->dateTime('fecha_inicio');

            $table->dateTime('fecha_fin')
                  ->nullable();

            $table->enum('estado', [
                'pendiente',
                'activa',
                'finalizada',
                'cancelada'
            ])->default('pendiente');

            $table->text('descripcion')
                  ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
