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
        Schema::create('aulas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('edificio_id')
                  ->constrained('edificios')
                  ->cascadeOnDelete();

            $table->string('numero', 10);
            $table->string('descripcion')->nullable();

            $table->timestamps();

            $table->unique(['edificio_id', 'numero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aulas');
    }
};
