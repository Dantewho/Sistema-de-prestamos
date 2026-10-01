<?php

namespace App\Exports;

use App\Models\Solicitud;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class SolicitudExport implements FromCollection
{
    public function collection(): Collection
    {
        return Solicitud::all();
    }
}
