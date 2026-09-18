<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventario';

    protected $fillable = ['nombre', 'cantidad', 'descripcion'];

    protected function casts(): array
    {
        return ['cantidad' => 'integer'];
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}