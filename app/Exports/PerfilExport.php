<?php

namespace App\Exports;

use App\Models\Perfil;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerfilExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
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
            'A' => 24,
            'B' => 20,
            'C' => 32,
            'D' => 20,
            'E' => 36,
            'F' => 14,
        ];
    }
}
