<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $fillable = ['edificio_id', 'numero', 'descripcion'];

    public function edificio()
    {
        return $this->belongsTo(Edificio::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}