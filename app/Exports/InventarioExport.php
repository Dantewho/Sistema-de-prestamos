<?php

namespace App\Exports;

use App\Models\Inventario;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class InventarioExport implements FromCollection
{
    public function collection(): Collection
    {
        return Inventario::all();
    }
}
