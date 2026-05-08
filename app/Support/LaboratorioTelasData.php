<?php

namespace App\Support;

use Illuminate\Support\Collection;

final class LaboratorioTelasData
{
    public static function lotes(): Collection
    {
        return collect([
            [
                'id' => 1001,
                'lote' => 'LT-ALG-2401',
                'tipo_tela' => 'Algodon peinado',
                'composicion' => '100% algodon',
                'resistencia_tension' => 42.8,
                'elongacion' => 18.4,
                'defectos' => 3,
                'estado' => 'Aprobado',
                'tecnico' => 'Mariana Ruiz',
                'fecha' => '2026-05-01',
            ],
            [
                'id' => 1002,
                'lote' => 'LT-POL-2402',
                'tipo_tela' => 'Poliester deportivo',
                'composicion' => '92% poliester / 8% elastano',
                'resistencia_tension' => 51.2,
                'elongacion' => 29.7,
                'defectos' => 6,
                'estado' => 'Observacion',
                'tecnico' => 'Daniel Soto',
                'fecha' => '2026-05-02',
            ],
            [
                'id' => 1003,
                'lote' => 'LT-MEZ-2403',
                'tipo_tela' => 'Mezclilla ligera',
                'composicion' => '78% algodon / 20% poliester / 2% elastano',
                'resistencia_tension' => 63.5,
                'elongacion' => 15.1,
                'defectos' => 2,
                'estado' => 'Aprobado',
                'tecnico' => 'Paula Mendez',
                'fecha' => '2026-05-03',
            ],
            [
                'id' => 1004,
                'lote' => 'LT-RAY-2404',
                'tipo_tela' => 'Rayon estampado',
                'composicion' => '96% rayon / 4% elastano',
                'resistencia_tension' => 34.9,
                'elongacion' => 24.3,
                'defectos' => 9,
                'estado' => 'Rechazado',
                'tecnico' => 'Mariana Ruiz',
                'fecha' => '2026-05-04',
            ],
            [
                'id' => 1005,
                'lote' => 'LT-LIN-2405',
                'tipo_tela' => 'Lino camisero',
                'composicion' => '55% lino / 45% viscosa',
                'resistencia_tension' => 39.6,
                'elongacion' => 12.8,
                'defectos' => 4,
                'estado' => 'Aprobado',
                'tecnico' => 'Daniel Soto',
                'fecha' => '2026-05-05',
            ],
            [
                'id' => 1006,
                'lote' => 'LT-NYL-2406',
                'tipo_tela' => 'Nylon tecnico',
                'composicion' => '88% nylon / 12% elastano',
                'resistencia_tension' => 58.1,
                'elongacion' => 33.5,
                'defectos' => 5,
                'estado' => 'Observacion',
                'tecnico' => 'Paula Mendez',
                'fecha' => '2026-05-06',
            ],
        ]);
    }

    public static function lotesArray(): array
    {
        return self::lotes()->values()->all();
    }

    public static function defectosPorTela(): array
    {
        return self::lotes()
            ->groupBy('tipo_tela')
            ->map(fn (Collection $lotes): int => (int) $lotes->sum('defectos'))
            ->all();
    }
}
