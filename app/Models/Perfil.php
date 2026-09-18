<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'usuario', 'email', 'password', 'tipo_usuario', 'imagen_perfil'])]
#[Hidden(['password', 'remember_token'])]
class Perfil extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'perfiles';

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function solicitudesRealizadas()
    {
        return $this->hasMany(Solicitud::class, 'usuario_solicitante_id');
    }

    public function solicitudesPrestadas()
    {
        return $this->hasMany(Solicitud::class, 'usuario_prestador_id');
    }
}