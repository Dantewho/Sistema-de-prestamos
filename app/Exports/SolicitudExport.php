<?php

namespace App\Exports;

use App\Models\Solicitud;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SolicitudExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function collection(): Collection
    {
        return Solicitud::with(['solicitante', 'prestador', 'aula.edificio', 'inventario'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Solicitante',
            'identificacion',
            'Prestador',
            'Tipo de solicitud',
            'Aula',
            'Inventario',
            'Cantidad',
            'Fecha de inicio',
            'Fecha de fin',
            'Estado',
            'Descripción',
            'Creado el',
            'Actualizado el',
        ];
    }

    public function map(mixed $solicitud): array
    {
        return [
            $solicitud->id,
            $solicitud->solicitante?->name,
            $solicitud->identificacion,
            $solicitud->prestador?->name,
            $solicitud->tipo_solicitud,
            $solicitud->aula === null
                ? null
                : $solicitud->aula->numero.'-'.$solicitud->aula->edificio?->nombre,
            $solicitud->inventario === null
                ? null
                : $solicitud->inventario?->nombre,
            // $solicitud->inventario?->nombre,
            $solicitud->cantidad,
            $solicitud->fecha_inicio,
            $solicitud->fecha_fin,
            $solicitud->estado,
            $solicitud->descripcion,
            $solicitud->created_at,
            $solicitud->updated_at,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Obtiene el rango ocupado para aplicar el formato a toda la tabla.
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Mantiene visible el encabezado al desplazarse y agrega filtros a las columnas.
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$lastColumn}{$lastRow}");

        // Centra verticalmente el contenido y agrega una línea inferior sutil a las celdas.
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_HAIR)
            ->getColor()->setARGB('FFD9E2F3');

        // Da al encabezado fondo azul, texto blanco en negrita y alinea sus títulos.
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF17365D'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Aumenta la altura del encabezado para que los títulos se lean con claridad.
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Colorea una fila sí y otra no para distinguir fácilmente cada registro.
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF2F6FA');
            }

            // Da una altura uniforme a las filas de datos.
            $sheet->getRowDimension($row)->setRowHeight(22);
        }

        // Los estilos ya se aplicaron directamente a la hoja.
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 24,
            'C' => 18,
            'D' => 24,
            'E' => 20,
            'F' => 14,
            'G' => 24,
            'H' => 12,
            'I' => 20,
            'J' => 20,
            'K' => 16,
            'L' => 36,
            'M' => 20,
            'N' => 20,
        ];
    }

    public function title(): string
    {
        return 'Reporte de solicitudes';
    }
}
