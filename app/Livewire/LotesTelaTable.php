<?php

namespace App\Livewire;

use App\Support\LaboratorioTelasData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class LotesTelaTable extends PowerGridComponent
{
    public string $tableName = 'lotes_tela_table';

    public function datasource(): Collection
    {
        return LaboratorioTelasData::lotes();
    }

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns(),

            PowerGrid::footer()
                ->showPerPage(5, [5, 10, 25, 0])
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('lote')
            ->add('tipo_tela')
            ->add('composicion')
            ->add('resistencia_tension')
            ->add('elongacion')
            ->add('defectos')
            ->add('estado')
            ->add('estado_badge', fn ($entry): string => $this->estadoBadge($entry->estado))
            ->add('tecnico')
            ->add('fecha_formateada', fn ($entry): string => Carbon::parse($entry->fecha)->format('d/m/Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('Lote', 'lote')
                ->searchable()
                ->sortable(),

            Column::make('Tipo de tela', 'tipo_tela')
                ->searchable()
                ->sortable(),

            Column::make('Composicion', 'composicion')
                ->searchable(),

            Column::make('Tension (N)', 'resistencia_tension')
                ->sortable(),

            Column::make('Elongacion (%)', 'elongacion')
                ->sortable(),

            Column::make('Defectos', 'defectos')
                ->sortable(),

            Column::make('Estado', 'estado_badge', 'estado')
                ->searchable()
                ->sortable(),

            Column::make('Tecnico', 'tecnico')
                ->searchable()
                ->sortable(),

            Column::make('Fecha', 'fecha_formateada', 'fecha')
                ->sortable(),
        ];
    }

    private function estadoBadge(string $estado): string
    {
        $classes = match ($estado) {
            'Aprobado' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
            'Observacion' => 'bg-amber-100 text-amber-700 ring-amber-600/20',
            'Rechazado' => 'bg-rose-100 text-rose-700 ring-rose-600/20',
            default => 'bg-zinc-100 text-zinc-700 ring-zinc-600/20',
        };

        return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$classes.'">'.$estado.'</span>';
    }
}
