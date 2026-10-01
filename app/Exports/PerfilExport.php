<?php

namespace App\Exports;

use App\Models\Perfil;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PerfilExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Perfil::all();
    }

    public function headings(): array
    {
        return ['name', 'usuario', 'email', 'tipo_usuario', 'imagen_perfil', 'password'];
    }

    public function map(mixed $perfil): array
    {
        return [
            $perfil->name,
            $perfil->usuario,
            $perfil->email,
            $perfil->tipo_usuario,
            $perfil->imagen_perfil,
            null,
        ];
    }
}
