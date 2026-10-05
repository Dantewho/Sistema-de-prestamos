<?php

namespace App\Imports;

use App\Models\Inventario;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class InventarioImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {

        return new Inventario([
            'nombre' => $row['nombre'],
            'cantidad' => $row['cantidad'],
            'descripcion' => $row['descripcion'] ?? null,

        ]);

    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}
