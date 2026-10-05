<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Support\Carbon;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

class DownloadInvoiceController extends Controller
{
    public function __invoke(Solicitud $solicitud): PdfBuilder
    {
        $solicitud->load(['solicitante', 'prestador', 'aula.edificio', 'inventario']);

        return Pdf::view('PDF.pdfPlantilla', ['solicitud' => $solicitud])
            ->format('a4')
            ->download("solicitud-{$solicitud->getKey()}.pdf");
    }

    public function exportarTodas(): PdfBuilder
    {
        $solicitudes = Solicitud::with(['solicitante', 'prestador', 'aula.edificio', 'inventario'])
            ->latest()
            ->get();

        return Pdf::view('PDF.pdfPlantilla', ['solicitudes' => $solicitudes])
            ->format('a4')
            ->download('solicitudes-'.Carbon::now()->format('Y-m-d').'.pdf');
    }
}
