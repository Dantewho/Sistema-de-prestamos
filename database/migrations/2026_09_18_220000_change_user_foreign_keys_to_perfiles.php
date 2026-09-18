<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('solicitudes')) {
            Schema::table('solicitudes', function (Blueprint $table) {
                $table->dropForeign(['usuario_solicitante_id']);
                $table->dropForeign(['usuario_prestador_id']);
                $table->foreign('usuario_solicitante_id')->references('id')->on('perfiles')->restrictOnDelete();
                $table->foreign('usuario_prestador_id')->references('id')->on('perfiles')->nullOnDelete();
            });
        }

        if (Schema::hasTable('passkeys')) {
            Schema::table('passkeys', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('perfiles')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('solicitudes')) {
            Schema::table('solicitudes', function (Blueprint $table) {
                $table->dropForeign(['usuario_solicitante_id']);
                $table->dropForeign(['usuario_prestador_id']);
                $table->foreign('usuario_solicitante_id')->references('id')->on('users')->restrictOnDelete();
                $table->foreign('usuario_prestador_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (Schema::hasTable('passkeys')) {
            Schema::table('passkeys', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }
};