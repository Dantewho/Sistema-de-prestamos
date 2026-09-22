<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'usuario_solicitante_id',
        'identificacion',
        'usuario_prestador_id',
        'tipo_solicitud',
        'aula_id',
        'inventario_id',
        'cantidad',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'descripcion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
            'cantidad' => 'integer',
        ];
    }

    public function solicitante()
    {
        return $this->belongsTo(Perfil::class, 'usuario_solicitante_id');
    }

    public function prestador()
    {
        return $this->belongsTo(Perfil::class, 'usuario_prestador_id');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }
}
