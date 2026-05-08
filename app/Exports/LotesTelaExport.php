<?php

namespace App\Exports;

use App\Support\LaboratorioTelasData;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class LotesTelaExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'ID',
            'Lote',
            'Tipo de tela',
            'Composicion',
            'Resistencia tension (N)',
            'Elongacion (%)',
            'Defectos',
            'Estado',
            'Tecnico',
            'Fecha',
        ];
    }

    public function array(): array
    {
        return LaboratorioTelasData::lotes()
            ->map(fn (array $lote): array => [
                $lote['id'],
                $lote['lote'],
                $lote['tipo_tela'],
                $lote['composicion'],
                $lote['resistencia_tension'],
                $lote['elongacion'],
                $lote['defectos'],
                $lote['estado'],
                $lote['tecnico'],
                $lote['fecha'],
            ])
            ->all();
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()->setFillType('solid')->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle('E:F')->getNumberFormat()->setFormatCode('0.0');

        return [];
    }
}
