<?php

namespace App\Exports;

use App\Models\Aula;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductosExport implements FromCollection
{
    public function collection(): Collection
    {
        return Aula::all();
    }
}
