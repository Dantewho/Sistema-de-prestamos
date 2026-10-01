<?php

namespace App\Imports;

use App\Models\Inventario;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductosImport implements ToModel
{
    public function model(array $row): Model|null
    {
        return new Inventario([
            //
        ]);
    }
}
